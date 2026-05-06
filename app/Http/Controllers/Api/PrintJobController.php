<?php

namespace App\Http\Controllers\Api;

use App\Enums\PrintJobStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrintJobRequest;
use App\Http\Requests\UpdatePrintJobRequest;
use App\Models\PrintFile;
use App\Models\PrintJob;
use App\Services\PrintFileAnalysisService;
use App\Services\PrintJobPricingService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrintJobController extends Controller
{
    public function userIndex(Request $request)
    {
        $perPage = max(1, min((int) $request->integer('per_page', 15), 50));

        $jobs = PrintJob::query()
            ->where('user_id', $request->user()->id)
            ->whereHas('printFile', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with([
                'material:id,name,slug,material_type',
                'printFile:id,original_name,file_extension',
            ])
            ->latest('id')
            ->paginate($perPage);

        return ApiResponse::paginated($jobs);
    }

    public function index(Request $request, PrintFile $printFile)
    {
        $this->ensureFileOwnership($request, $printFile);

        $jobs = $printFile->printJobs()
            ->where('user_id', $request->user()->id)
            ->with([
                'material:id,name,slug,material_type',
                'printFile:id,original_name,file_extension',
            ])
            ->latest('id')
            ->paginate(15);

        return ApiResponse::paginated($jobs);
    }

    public function store(
        StorePrintJobRequest $request,
        PrintFile $printFile,
        PrintFileAnalysisService $analysisService,
        PrintJobPricingService $pricingService
    ) {
        $this->ensureFileOwnership($request, $printFile);

        $data = $request->validated();

        $job = DB::transaction(function () use ($request, $printFile, $data, $analysisService, $pricingService) {
            $job = PrintJob::query()->create([
                'user_id' => $request->user()->id,
                'print_file_id' => $printFile->id,
                'material_id' => $data['material_id'],
                'technology' => $data['technology'],
                'color_name' => $data['color_name'] ?? null,
                'quantity' => $data['quantity'],
                'infill_percent' => $data['infill_percent'],
                'scale_percent' => $data['scale_percent'],
                'status' => PrintJobStatus::Draft,
            ]);

            $job->load('material:id,material_type');

            $analysis = $analysisService->analyze($printFile, [
                'quantity' => $job->quantity,
                'scale_percent' => $job->scale_percent,
                'infill_percent' => $job->infill_percent,
                'technology' => $job->technology,
                'material_type' => $job->material?->material_type,
            ]);

            $job->fill([
                'estimated_material_g' => $analysis['estimated_material_g'],
                'estimated_time_min' => $analysis['estimated_time_min'],
                'estimated_volume_cm3' => $analysis['estimated_volume_cm3'],
                'analysis_source' => $analysis['analysis_source'],
            ]);

            $quote = $pricingService->quote($job, false);

            $status = !empty($analysis['manual_review_required'])
                ? PrintJobStatus::ReviewPending
                : PrintJobStatus::Priced;

            $job->fill([
                'estimated_material_g' => $quote['estimated_material_g'],
                'estimated_time_min' => $quote['estimated_time_min'],
                'estimated_volume_cm3' => $quote['estimated_volume_cm3'],
                'analysis_source' => $quote['analysis_source'],
                'unit_price' => $quote['unit_price'],
                'pricing_breakdown' => array_merge(
                    $quote['pricing_breakdown'],
                    [
                        'analysis_details' => $analysis['analysis'] ?? [],
                        'manual_review_required' => (bool) ($analysis['manual_review_required'] ?? false),
                        'review_reasons' => $analysis['review_reasons'] ?? [],
                    ]
                ),
                'status' => $status,
            ]);

            $job->save();

            return $job;
        });

        $job->load([
            'material:id,name,slug,material_type',
            'printFile:id,original_name,file_extension',
        ]);

        return ApiResponse::created(
            data: $job,
            message: 'PrintJob procesado correctamente.'
        );
    }

    public function show(Request $request, PrintFile $printFile, PrintJob $printJob)
    {
        $this->ensureFileOwnership($request, $printFile);
        $this->ensureJobBelongsToFile($printFile, $printJob);
        $this->ensureJobOwnership($request, $printJob);

        $printJob->load([
            'material:id,name,slug,material_type',
            'printFile:id,original_name,file_extension',
        ]);

        return ApiResponse::success($printJob);
    }

    public function update(
        UpdatePrintJobRequest $request,
        PrintFile $printFile,
        PrintJob $printJob,
        PrintFileAnalysisService $analysisService,
        PrintJobPricingService $pricingService
    ) {
        $this->ensureFileOwnership($request, $printFile);
        $this->ensureJobBelongsToFile($printFile, $printJob);
        $this->ensureJobOwnership($request, $printJob);

        abort_unless(
            $printJob->isEditableByUser(),
            422,
            'Este trabajo de impresión no se puede modificar en su estado actual.'
        );

        $job = DB::transaction(function () use ($request, $printFile, $printJob, $analysisService, $pricingService) {
            $lockedJob = PrintJob::query()
                ->whereKey($printJob->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $lockedJob->isEditableByUser(),
                422,
                'Este trabajo de impresión no se puede modificar en su estado actual.'
            );

            $lockedJob->fill($request->validated());
            $lockedJob->load('material:id,material_type');

            $analysis = $analysisService->analyze($printFile, [
                'quantity' => $lockedJob->quantity,
                'scale_percent' => $lockedJob->scale_percent,
                'infill_percent' => $lockedJob->infill_percent,
                'technology' => $lockedJob->technology,
                'material_type' => $lockedJob->material?->material_type,
            ]);

            $lockedJob->fill([
                'estimated_material_g' => $analysis['estimated_material_g'],
                'estimated_time_min' => $analysis['estimated_time_min'],
                'estimated_volume_cm3' => $analysis['estimated_volume_cm3'],
                'analysis_source' => $analysis['analysis_source'],
            ]);

            $quote = $pricingService->quote($lockedJob, false);

            $status = !empty($analysis['manual_review_required'])
                ? PrintJobStatus::ReviewPending
                : PrintJobStatus::Priced;

            $lockedJob->fill([
                'estimated_material_g' => $quote['estimated_material_g'],
                'estimated_time_min' => $quote['estimated_time_min'],
                'estimated_volume_cm3' => $quote['estimated_volume_cm3'],
                'analysis_source' => $quote['analysis_source'],
                'unit_price' => $quote['unit_price'],
                'pricing_breakdown' => array_merge(
                    $quote['pricing_breakdown'],
                    [
                        'analysis_details' => $analysis['analysis'] ?? [],
                        'manual_review_required' => (bool) ($analysis['manual_review_required'] ?? false),
                        'review_reasons' => $analysis['review_reasons'] ?? [],
                    ]
                ),
                'status' => $status,
            ]);

            $lockedJob->save();

            return $lockedJob;
        });

        $job->load([
            'material:id,name,slug,material_type',
            'printFile:id,original_name,file_extension',
        ]);

        return ApiResponse::success(
            data: $job,
            message: 'PrintJob actualizado correctamente.'
        );
    }

    public function recalculate(
        Request $request,
        PrintFile $printFile,
        PrintJob $printJob,
        PrintFileAnalysisService $analysisService,
        PrintJobPricingService $pricingService
    ) {
        $this->ensureFileOwnership($request, $printFile);
        $this->ensureJobBelongsToFile($printFile, $printJob);
        $this->ensureJobOwnership($request, $printJob);

        abort_unless(
            $printJob->isEditableByUser(),
            422,
            'Este trabajo de impresión no se puede recalcular en su estado actual.'
        );

        $job = DB::transaction(function () use ($printFile, $printJob, $analysisService, $pricingService) {
            $lockedJob = PrintJob::query()
                ->whereKey($printJob->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $lockedJob->isEditableByUser(),
                422,
                'Este trabajo de impresión no se puede recalcular en su estado actual.'
            );

            $lockedJob->load('material:id,material_type');

            $analysis = $analysisService->analyze($printFile, [
                'quantity' => $lockedJob->quantity,
                'scale_percent' => $lockedJob->scale_percent,
                'infill_percent' => $lockedJob->infill_percent,
                'technology' => $lockedJob->technology,
                'material_type' => $lockedJob->material?->material_type,
            ]);

            $lockedJob->fill([
                'estimated_material_g' => $analysis['estimated_material_g'],
                'estimated_time_min' => $analysis['estimated_time_min'],
                'estimated_volume_cm3' => $analysis['estimated_volume_cm3'],
                'analysis_source' => $analysis['analysis_source'],
            ]);

            $quote = $pricingService->quote($lockedJob, false);

            $status = !empty($analysis['manual_review_required'])
                ? PrintJobStatus::ReviewPending
                : PrintJobStatus::Priced;

            $lockedJob->fill([
                'estimated_material_g' => $quote['estimated_material_g'],
                'estimated_time_min' => $quote['estimated_time_min'],
                'estimated_volume_cm3' => $quote['estimated_volume_cm3'],
                'analysis_source' => $quote['analysis_source'],
                'unit_price' => $quote['unit_price'],
                'pricing_breakdown' => array_merge(
                    $quote['pricing_breakdown'],
                    [
                        'analysis_details' => $analysis['analysis'] ?? [],
                        'manual_review_required' => (bool) ($analysis['manual_review_required'] ?? false),
                        'review_reasons' => $analysis['review_reasons'] ?? [],
                    ]
                ),
                'status' => $status,
            ]);

            $lockedJob->save();

            return $lockedJob;
        });

        $job->load([
            'material:id,name,slug,material_type',
            'printFile:id,original_name,file_extension',
        ]);

        return ApiResponse::success(
            data: $job,
            message: 'PrintJob recalculado correctamente.'
        );
    }

    public function continueWithoutReview(
        Request $request,
        PrintFile $printFile,
        PrintJob $printJob,
        PrintJobPricingService $pricingService
    ) {
        $this->ensureFileOwnership($request, $printFile);
        $this->ensureJobBelongsToFile($printFile, $printJob);
        $this->ensureJobOwnership($request, $printJob);

        abort_unless(
            ($printJob->status?->value ?? (string) $printJob->status) === PrintJobStatus::ReviewPending->value,
            422,
            'Este trabajo no está pendiente de revisión.'
        );

        $job = DB::transaction(function () use ($printJob, $pricingService) {
            $lockedJob = PrintJob::query()
                ->whereKey($printJob->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                ($lockedJob->status?->value ?? (string) $lockedJob->status) === PrintJobStatus::ReviewPending->value,
                422,
                'Este trabajo no está pendiente de revisión.'
            );

            $quote = $pricingService->quote($lockedJob, true);

            $breakdown = $quote['pricing_breakdown'];
            $breakdown['continued_without_review'] = true;

            $lockedJob->fill([
                'estimated_material_g' => $quote['estimated_material_g'],
                'estimated_time_min' => $quote['estimated_time_min'],
                'estimated_volume_cm3' => $quote['estimated_volume_cm3'],
                'analysis_source' => $quote['analysis_source'],
                'unit_price' => $quote['unit_price'],
                'pricing_breakdown' => $breakdown,
                'status' => PrintJobStatus::Priced,
            ]);

            $lockedJob->save();

            return $lockedJob;
        });

        $job->load([
            'material:id,name,slug,material_type',
            'printFile:id,original_name,file_extension',
        ]);

        return ApiResponse::success(
            data: $job,
            message: 'PrintJob validado sin revisión manual.'
        );
    }

    public function destroy(Request $request, PrintFile $printFile, PrintJob $printJob)
    {
        $this->ensureFileOwnership($request, $printFile);
        $this->ensureJobBelongsToFile($printFile, $printJob);
        $this->ensureJobOwnership($request, $printJob);

        abort_unless(
            $printJob->isDeletableByUser(),
            422,
            'Este trabajo de impresión no se puede eliminar en su estado actual.'
        );

        DB::transaction(function () use ($printJob) {
            $lockedJob = PrintJob::query()
                ->whereKey($printJob->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $lockedJob->isDeletableByUser(),
                422,
                'Este trabajo de impresión no se puede eliminar en su estado actual.'
            );

            $lockedJob->delete();
        });

        return ApiResponse::success(
            data: null,
            message: 'PrintJob eliminado correctamente.'
        );
    }

    private function ensureFileOwnership(Request $request, PrintFile $printFile): void
    {
        abort_unless((int) $printFile->user_id === (int) $request->user()->id, 403);
    }

    private function ensureJobOwnership(Request $request, PrintJob $printJob): void
    {
        abort_unless((int) $printJob->user_id === (int) $request->user()->id, 403);
    }

    private function ensureJobBelongsToFile(PrintFile $printFile, PrintJob $printJob): void
    {
        abort_unless((int) $printJob->print_file_id === (int) $printFile->id, 404);
    }
}
