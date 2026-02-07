<?php

namespace App\Http\Controllers\Api;

use App\Enums\PrintFileStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrintFileRequest;
use App\Models\PrintFile;
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
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', (string) $request->query('status'));
        }

        $printFiles = $query->paginate(20);

        return response()->json([
            'data' => $printFiles->items(),
            'meta' => [
                'current_page' => $printFiles->currentPage(),
                'per_page' => $printFiles->perPage(),
                'total' => $printFiles->total(),
                'last_page' => $printFiles->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, PrintFile $printFile)
    {
        abort_unless($printFile->user_id === $request->user()->id, 403);

        return response()->json([
            'data' => $printFile,
        ]);
    }

    public function store(StorePrintFileRequest $request)
    {
        $file = $request->file('file');
        $userId = $request->user()->id;

        DB::beginTransaction();

        try {
            $printFile = PrintFile::create([
                'user_id'       => $userId,
                'original_name' => $file->getClientOriginalName(),
                'status'        => PrintFileStatus::Uploaded,
                'notes'         => $request->input('notes'),
                'storage_path'  => '', // se actualiza después
            ]);

            $directory = "print-files/{$userId}/{$printFile->id}";
            $filename  = $file->getClientOriginalName();
            $path      = "{$directory}/{$filename}";

            Storage::disk('local')->putFileAs($directory, $file, $filename);

            $printFile->update([
                'storage_path'   => $path,
                'mime_type'      => $file->getClientMimeType(),
                'file_extension' => $file->getClientOriginalExtension(),
                'file_size'      => $file->getSize(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Archivo subido correctamente',
                'data' => $printFile,
            ], 201);

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
}
