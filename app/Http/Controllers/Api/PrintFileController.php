<?php

namespace App\Http\Controllers\Api;

use App\Enums\PrintFileStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrintFileRequest;
use App\Models\PrintFile;
use App\Models\PrintFileAnalysis;
use App\Services\PrintFileAnalysisService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PrintFileController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $query = PrintFile::query()
            ->where('user_id', $userId)
            ->with('analysis')
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', (string) $request->query('status'));
        }

        $printFiles = $query->paginate(20);

        return ApiResponse::paginated($printFiles);
    }

    public function show(Request $request, PrintFile $printFile)
    {
        abort_unless($printFile->user_id === $request->user()->id, 403);

        $printFile->load('analysis');

        return ApiResponse::success($printFile);
    }

    public function store(StorePrintFileRequest $request, PrintFileAnalysisService $analysisService)
    {
        $file = $request->file('file');
        $userId = $request->user()->id;

        DB::beginTransaction();

        try {
            $printFile = PrintFile::create([
                'user_id' => $userId,
                'original_name' => $file->getClientOriginalName(),
                'status' => PrintFileStatus::Uploaded,
                'notes' => $request->input('notes'),
                'storage_path' => '',
            ]);

            $directory = "print-files/{$userId}/{$printFile->id}";
            $filename = $file->getClientOriginalName();
            $path = "{$directory}/{$filename}";

            Storage::disk('local')->putFileAs($directory, $file, $filename);

            $printFile->update([
                'storage_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_extension' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
            ]);

            try {
                $baseAnalysis = $analysisService->analyzeBase($printFile);
            } catch (\Throwable $e) {
                $baseAnalysis = [
                    'estimated_volume_cm3' => null,
                    'estimated_material_g' => null,
                    'estimated_time_min' => null,
                    'dimensions_mm' => null,
                    'triangle_count' => null,
                    'analysis_source' => 'analysis_error',
                    'analysis_details' => [
                        'notes' => [
                            'No se ha podido completar el análisis automático del archivo en el momento de la subida.',
                        ],
                    ],
                    'manual_review_required' => true,
                    'review_reasons' => [
                        'No se ha podido completar el análisis automático del archivo.',
                    ],
                ];
            }

            PrintFileAnalysis::updateOrCreate(
                ['print_file_id' => $printFile->id],
                [
                    'estimated_volume_cm3' => $baseAnalysis['estimated_volume_cm3'],
                    'estimated_material_g' => $baseAnalysis['estimated_material_g'],
                    'estimated_time_min' => $baseAnalysis['estimated_time_min'],
                    'dimensions_mm' => $baseAnalysis['dimensions_mm'],
                    'triangle_count' => $baseAnalysis['triangle_count'],
                    'analysis_source' => $baseAnalysis['analysis_source'],
                    'analysis_details' => $baseAnalysis['analysis_details'],
                    'manual_review_required' => $baseAnalysis['manual_review_required'],
                    'review_reasons' => $baseAnalysis['review_reasons'],
                ]
            );

            DB::commit();

            $printFile->refresh()->load('analysis');

            return ApiResponse::created(
                data: $printFile,
                message: 'Archivo subido correctamente'
            );
        } catch (\Throwable $e) {
            DB::rollBack();

            if (isset($path) && Storage::disk('local')->exists($path)) {
                Storage::disk('local')->deleteDirectory($directory);
            }

            throw $e;
        }
    }

    public function download(Request $request, PrintFile $printFile)
    {
        abort_unless($printFile->user_id === $request->user()->id, 403);

        $relativePath = $printFile->storage_path;

        abort_unless($relativePath && Storage::disk('local')->exists($relativePath), 404);

        return Storage::disk('local')->download($relativePath, $printFile->original_name);
    }

    public function destroy(Request $request, PrintFile $printFile)
    {
        abort_unless((int) $printFile->user_id === (int) $request->user()->id, 403);

        $activeJobs = $printFile->printJobs()
            ->where('user_id', $request->user()->id)
            ->get();

        $nonDeletableJob = $activeJobs->first(fn ($job) => !$job->isDeletableByUser());

        abort_unless(
            $nonDeletableJob === null,
            422,
            'Este archivo no se puede eliminar porque tiene trabajos asociados en un estado no borrable.'
        );

        DB::transaction(function () use ($printFile, $activeJobs) {
            foreach ($activeJobs as $job) {
                $job->delete();
            }

            $printFile->analysis()->delete();
            $printFile->delete();
        });

        $directory = $printFile->storage_path
            ? dirname($printFile->storage_path)
            : "print-files/{$printFile->user_id}/{$printFile->id}";

        Storage::disk('local')->deleteDirectory($directory);

        return ApiResponse::success(
            data: null,
            message: 'Archivo eliminado correctamente.'
        );
    }
}
