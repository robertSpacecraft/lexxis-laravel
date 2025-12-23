<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PrintFileStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrintFileRequest;
use App\Http\Requests\UpdatePrintFileRequest;
use Illuminate\Http\Request;
use App\Models\PrintFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PrintFileController extends Controller
{
    public function index(Request $request){
        $query = PrintFile::query()
            ->with('user:id,name,last_name,email')
            ->orderBy('id');

        if ($request->filled('status')){
            $query->where('status', $request->string('status')->toString());
        }
        if ($request->filled('user_id')){
            $query->where('user_id', $request->integer('user_id'));
        }
        $printFiles = $query->paginate(20)->withQueryString();
        return view('admin.print-files.index', compact('printFiles'));
    }

    public function create()
    {
        return view('admin.print-files.create');
    }

    public function show(PrintFile $printFile)
    {
        // Admin: acceso total. Si más adelante hay user-panel, aquí se comprobará ownership.
        return view('admin.print-files.show', compact('printFile'));
    }


    public function store(StorePrintFileRequest $request)
    {
        $file = $request->file('file');
        $userId = auth()->id();

        DB::beginTransaction();

        try {
            //Creo el registro base con el storage_path aún NULL
            $printFile = PrintFile::create([
                'user_id'       => $userId,
                'original_name' => $file->getClientOriginalName(),
                'status'        => PrintFileStatus::Uploaded,
                'notes'         => $request->input('notes'),
            ]);

            //Construyo la ruta
            $directory = "print-files/{$userId}/{$printFile->id}";
            $filename  = $file->getClientOriginalName();
            $path      = "{$directory}/{$filename}";

            //Guardar el archivo en storage privado
            Storage::disk('local')->putFileAs(
                $directory,
                $file,
                $filename
            );

            //Actualizo los metadatos
            $printFile->update([
                'storage_path'   => $path,
                'mime_type'      => $file->getClientMimeType(),
                'file_extension' => $file->getClientOriginalExtension(),
                'file_size'      => $file->getSize(),
            ]);

            DB::commit();

            return redirect()
                ->route('admin.print-files.index')
                ->with('success', 'Archivo subido correctamente.');

        } catch (\Throwable $e) {

            DB::rollBack();

            //Si el archivo no se escribe lo borro todo para que no queden líneas parciales en la BD
            if (isset($path) && Storage::disk('local')->exists($path)) {
                Storage::disk('local')->deleteDirectory($directory);
            }

            throw $e;
        }
    }

    public function update(UpdatePrintFileRequest $request, PrintFile $printFile)
    {
        $data = $request->validated();

        $metadata = null;
        if (!empty($data['metadata'])) {
            $metadata = json_decode($data['metadata'], true);
        }

        $printFile->update([
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
            'metadata' => $metadata,
        ]);

        return redirect()
            ->route('admin.print-files.show', $printFile)
            ->with('success', 'Archivo actualizado correctamente.');
    }

    public function edit(PrintFile $printFile)
    {
        $statuses = PrintFileStatus::cases();

        return view('admin.print-files.edit', compact('printFile', 'statuses'));
    }

    public function destroy(PrintFile $printFile)
    {
        // Directorio donde debería estar el archivo (seguro incluso si storage_path es null)
        $directory = $printFile->storage_path
            ? dirname($printFile->storage_path)
            : "print-files/{$printFile->user_id}/{$printFile->id}";

        // Borrado físico (si no existe, no pasa nada)
        Storage::disk('local')->deleteDirectory($directory);

        // Borrado lógico (SoftDelete)
        $printFile->delete();

        return redirect()
            ->route('admin.print-files.index')
            ->with('success', 'Archivo eliminado correctamente.');
    }

    //Descarga del archivo
    public function download(PrintFile $printFile)
    {
        $relativePath = $printFile->storage_path;

        abort_unless(Storage::disk('local')->exists($relativePath), 404);

        return Storage::disk('local')->download(
            $relativePath,
            $printFile->original_name
        );
    }


}
