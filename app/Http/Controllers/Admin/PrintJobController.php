<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PrintJobStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrintJobRequest;
use App\Http\Requests\UpdatePrintJobRequest;
use App\Models\Material;
use App\Models\PrintFile;
use App\Models\PrintJob;
use App\Services\PrintFileAnalysisService;
use App\Services\PrintJobPricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrintJobController extends Controller
{
    public function reviewPending(Request $request)
    {
        $printJobs = PrintJob::query()
            ->where('status', PrintJobStatus::ReviewPending->value)
            ->with([
                'material:id,name',
                'printFile:id,original_name,user_id',
                'user:id,name,last_name,email',
            ])
            ->latest('id')
            ->paginate(20);

        return view('admin.print-files.jobs.review-pending', compact('printJobs'));
    }

    public function index(PrintFile $printFile)
    {
        $printJobs = $printFile->printJobs()
            ->with(['material:id,name'])
            ->latest()
            ->paginate(20);

        return view('admin.print-files.jobs.index', compact('printFile', 'printJobs'));
    }

    public function create(PrintFile $printFile)
    {
        $materials = Material::query()
            ->select('id', 'name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.print-files.jobs.create', compact('printFile', 'materials'));
    }

    public function store(
        StorePrintJobRequest $request,
        PrintFile $printFile,
        PrintFileAnalysisService $analysisService,
        PrintJobPricingService $pricingService
    ) {
        $data = $request->validated();
        $data['print_file_id'] = $printFile->id;
        $data['user_id'] = $printFile->user_id;
        $data['status'] = PrintJobStatus::Draft;

        $printJob = DB::transaction(function () use ($data, $printFile, $analysisService, $pricingService) {
            $printJob = PrintJob::create($data);

            $printJob->load('material:id,material_type');

            $analysis = $analysisService->analyze($printFile, [
                'quantity' => $printJob->quantity,
                'scale_percent' => $printJob->scale_percent,
                'infill_percent' => $printJob->infill_percent,
                'technology' => $printJob->technology,
                'material_type' => $printJob->material?->material_type,
            ]);

            $printJob->fill([
                'estimated_material_g' => $analysis['estimated_material_g'],
                'estimated_time_min' => $analysis['estimated_time_min'],
                'estimated_volume_cm3' => $analysis['estimated_volume_cm3'],
                'analysis_source' => $analysis['analysis_source'],
            ]);

            $quote = $pricingService->quote($printJob, false);

            $status = !empty($analysis['manual_review_required'])
                ? PrintJobStatus::ReviewPending
                : PrintJobStatus::Priced;

            $printJob->fill([
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

            $printJob->save();

            return $printJob;
        });

        return redirect()
            ->route('admin.print-files.jobs.show', [$printFile, $printJob])
            ->with('success', 'PrintJob creado correctamente.');
    }

    public function show(PrintFile $printFile, PrintJob $printJob)
    {
        abort_unless((int) $printJob->print_file_id === (int) $printFile->id, 404);

        $printJob->load([
            'material:id,name',
            'printFile:id,original_name,user_id',
        ]);

        return view('admin.print-files.jobs.show', compact('printFile', 'printJob'));
    }

    public function edit(PrintFile $printFile, PrintJob $printJob)
    {
        abort_unless((int) $printJob->print_file_id === (int) $printFile->id, 404);

        $status = $printJob->status?->value ?? (string) $printJob->status;

        if (!in_array($status, [
            PrintJobStatus::Draft->value,
            PrintJobStatus::Priced->value,
            PrintJobStatus::ReviewPending->value,
        ], true)) {
            return redirect()
                ->route('admin.print-files.jobs.show', [$printFile, $printJob])
                ->with('error', 'Este PrintJob no se puede editar en su estado actual.');
        }

        $materials = Material::query()
            ->select('id', 'name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.print-files.jobs.edit', compact('printFile', 'printJob', 'materials'));
    }

    public function update(
        UpdatePrintJobRequest $request,
        PrintFile $printFile,
        PrintJob $printJob,
        PrintFileAnalysisService $analysisService,
        PrintJobPricingService $pricingService
    ) {
        abort_unless((int) $printJob->print_file_id === (int) $printFile->id, 404);

        $status = $printJob->status?->value ?? (string) $printJob->status;

        if (!in_array($status, [
            PrintJobStatus::Draft->value,
            PrintJobStatus::Priced->value,
            PrintJobStatus::ReviewPending->value,
        ], true)) {
            return redirect()
                ->route('admin.print-files.jobs.show', [$printFile, $printJob])
                ->with('error', 'Este PrintJob no se puede actualizar en su estado actual.');
        }

        $printJob = DB::transaction(function () use ($request, $printFile, $printJob, $analysisService, $pricingService) {
            $lockedJob = PrintJob::query()
                ->whereKey($printJob->id)
                ->lockForUpdate()
                ->firstOrFail();

            $data = $request->validated();
            unset($data['print_file_id'], $data['user_id']);

            $lockedJob->fill($data);
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

            $newStatus = !empty($analysis['manual_review_required'])
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
                'status' => $newStatus,
            ]);

            $lockedJob->save();

            return $lockedJob;
        });

        return redirect()
            ->route('admin.print-files.jobs.show', [$printFile, $printJob])
            ->with('success', 'PrintJob actualizado correctamente.');
    }

    public function approveReview(
        PrintFile $printFile,
        PrintJob $printJob,
        PrintJobPricingService $pricingService
    ) {
        abort_unless((int) $printJob->print_file_id === (int) $printFile->id, 404);

        abort_unless(
            ($printJob->status?->value ?? (string) $printJob->status) === PrintJobStatus::ReviewPending->value,
            422
        );

        DB::transaction(function () use ($printJob, $pricingService) {
            $lockedJob = PrintJob::query()
                ->whereKey($printJob->id)
                ->lockForUpdate()
                ->firstOrFail();

            $quote = $pricingService->quote($lockedJob, false);

            $breakdown = $quote['pricing_breakdown'];
            $breakdown['approved_by_admin'] = true;
            $breakdown['manual_review_required'] = false;
            $breakdown['review_reasons'] = [];

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
        });

        return redirect()
            ->route('admin.print-files.jobs.show', [$printFile, $printJob])
            ->with('success', 'PrintJob validado correctamente.');
    }

    public function destroy(PrintFile $printFile, PrintJob $printJob)
    {
        abort_unless((int) $printJob->print_file_id === (int) $printFile->id, 404);

        $printJob->delete();

        return redirect()
            ->route('admin.print-files.jobs.index', $printFile)
            ->with('success', 'PrintJob eliminado correctamente.');
    }
}
