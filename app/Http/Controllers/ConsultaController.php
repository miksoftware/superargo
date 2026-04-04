<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\ConsultaResult;
use App\Imports\CedulasImport;
use App\Exports\ResultsExport;
use App\Services\SuperargoService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ConsultaController extends Controller
{
    public function index()
    {
        $consultas = Consulta::with('user')
            ->when(!auth()->user()->isAdmin(), fn($q) => $q->where('status', 'completed'))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('consultas.index', compact('consultas'));
    }

    public function upload(Request $request)
    {
        $file = $request->file('archivo');

        if (!$file) {
            return back()->with('error', 'Selecciona un archivo.');
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
            return back()->with('error', 'Solo se permiten archivos .xlsx, .xls o .csv');
        }

        $import = new CedulasImport();
        Excel::import($import, $file);

        if ($import->cedulas->isEmpty()) {
            return back()->with('error', 'No se encontraron cédulas válidas en el archivo.');
        }

        $consulta = Consulta::create([
            'user_id' => auth()->id(),
            'filename' => $file->getClientOriginalName(),
            'total_cedulas' => $import->cedulas->count(),
            'status' => 'pending',
        ]);

        foreach ($import->cedulas as $cedula) {
            ConsultaResult::create([
                'consulta_id' => $consulta->id,
                'cedula' => $cedula,
            ]);
        }

        return redirect()->route('consultas.process', $consulta);
    }

    public function process(Consulta $consulta)
    {
        return view('consultas.process', compact('consulta'));
    }

    public function processNext(Consulta $consulta, SuperargoService $service)
    {
        try {
            $result = $consulta->pendingResults()->first();

            if (!$result) {
                $consulta->update(['status' => 'completed']);
                return response()->json(['done' => true, 'message' => 'Todas las cédulas procesadas.']);
            }

            $consulta->update(['status' => 'processing']);

            $data = $service->consultar($result->cedula);

            // Solo actualizar campos que existen en fillable
            $fillable = array_flip($result->getFillable());
            $updateData = array_intersect_key($data, $fillable);
            $updateData['processed'] = true;

            // Convertir strings vacías a null para evitar errores en MySQL strict mode
            foreach ($updateData as $key => $value) {
                if ($value === '' || $value === false) {
                    $updateData[$key] = null;
                }
            }
            // found debe ser boolean/int, no null
            if (isset($data['found'])) {
                $updateData['found'] = $data['found'] ? 1 : 0;
            }

            $result->update($updateData);
            $consulta->increment('processed');

            return response()->json([
                'done' => false,
                'result' => $result->fresh(),
                'processed' => $consulta->fresh()->processed,
                'total' => $consulta->total_cedulas,
            ]);
        } catch (\Throwable $e) {
            \Log::error("processNext error consulta={$consulta->id}: " . $e->getMessage());
            return response()->json([
                'done' => false,
                'error' => $e->getMessage(),
                'result' => [
                    'cedula' => $result->cedula ?? '?',
                    'found' => false,
                    'error' => 'Error interno: ' . $e->getMessage(),
                ],
                'processed' => $consulta->processed,
                'total' => $consulta->total_cedulas,
            ], 200); // 200 para que el JS lo procese como resultado fallido, no como crash
        }
    }

    public function pause(Consulta $consulta)
    {
        $consulta->update(['status' => 'paused']);
        return response()->json(['ok' => true]);
    }

    public function show(Consulta $consulta)
    {
        $results = $consulta->results()->where('processed', true)->paginate(50);
        return view('consultas.show', compact('consulta', 'results'));
    }

    public function export(Consulta $consulta)
    {
        $filename = 'resultados_' . $consulta->id . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new ResultsExport($consulta), $filename);
    }

    public function search(Request $request)
    {
        $cedula = $request->input('cedula');
        $results = null;

        if ($cedula) {
            $query = ConsultaResult::where('cedula', $cedula)->where('processed', true)->where('found', true);

            if (!auth()->user()->isAdmin()) {
                $query->whereHas('consulta', fn($q) => $q->where('status', 'completed'));
            }

            $results = $query->with('consulta')->orderByDesc('created_at')->get();
        }

        return view('consultas.search', compact('cedula', 'results'));
    }

    public function files()
    {
        $consultas = Consulta::where('status', 'completed')
            ->withCount(['results as found_count' => fn($q) => $q->where('found', true)])
            ->orderByDesc('created_at')
            ->get();

        return view('consultas.files', compact('consultas'));
    }
}
