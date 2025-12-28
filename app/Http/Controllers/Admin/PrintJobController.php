<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PrintJobStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrintJobRequest;
use App\Http\Requests\UpdatePrintJobRequest;
use App\Models\Material;
use App\Models\PrintFile;
use App\Models\PrintJob;

class PrintJobController extends Controller
{
    // GET /admin/print-files/{printFile}/jobs
    public function index(PrintFile $printFile)
    {
        $printJobs = $printFile->printJobs()
            ->with(['material:id,name'])
            ->latest()
            ->paginate(20);

        return view('admin.print-files.jobs.index', compact('printFile', 'printJobs'));
    }

    // GET /admin/print-files/{printFile}/jobs/create
    public function create(PrintFile $printFile)
    {
        $materials = Material::query()
            ->select('id', 'name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.print-files.jobs.create', compact('printFile', 'materials'));
    }

    // POST /admin/print-files/{printFile}/jobs
    public function store(StorePrintJobRequest $request, PrintFile $printFile)
    {
        $data = $request->validated();

        // Fuente de verdad: ruta anidada
        $data['print_file_id'] = $printFile->id;

        // El job pertenece al propietario del archivo (no editable desde el formulario)
        $data['user_id'] = $printFile->user_id;

        $data['status'] = PrintJobStatus::Draft;

        $printJob = PrintJob::create($data);

        return redirect()
            ->route('admin.print-files.jobs.show', [$printFile, $printJob])
            ->with('success', 'PrintJob creado correctamente.');
    }

    // GET /admin/print-files/{printFile}/jobs/{printJob}
    public function show(PrintFile $printFile, PrintJob $printJob)
    {
        abort_unless((int) $printJob->print_file_id === (int) $printFile->id, 404);

        $printJob->load([
            'material:id,name',
            'printFile:id,original_name,user_id',
        ]);

        return view('admin.print-files.jobs.show', compact('printFile', 'printJob'));
    }

    // GET /admin/print-files/{printFile}/jobs/{printJob}/edit
    public function edit(PrintFile $printFile, PrintJob $printJob)
    {
        abort_unless((int) $printJob->print_file_id === (int) $printFile->id, 404);

        $materials = Material::query()
            ->select('id', 'name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.print-files.jobs.edit', compact('printFile', 'printJob', 'materials'));
    }

    // PUT /admin/print-files/{printFile}/jobs/{printJob}
    public function update(UpdatePrintJobRequest $request, PrintFile $printFile, PrintJob $printJob)
    {
        abort_unless((int) $printJob->print_file_id === (int) $printFile->id, 404);

        $data = $request->validated();

        // Blindaje: estos campos no se reasignan desde update
        unset($data['print_file_id'], $data['user_id']);

        $printJob->update($data);

        return redirect()
            ->route('admin.print-files.jobs.show', [$printFile, $printJob])
            ->with('success', 'PrintJob actualizado correctamente.');
    }

    // DELETE /admin/print-files/{printFile}/jobs/{printJob}
    public function destroy(PrintFile $printFile, PrintJob $printJob)
    {
        abort_unless((int) $printJob->print_file_id === (int) $printFile->id, 404);

        $printJob->delete();

        return redirect()
            ->route('admin.print-files.jobs.index', $printFile)
            ->with('success', 'PrintJob eliminado correctamente.');
    }
}
