<?php

namespace App\Http\Controllers;

use App\Models\Medicamento;
use Illuminate\Http\Request;
use App\Imports\MedicamentosImport;
use Maatwebsite\Excel\Facades\Excel;

class MedicamentoController extends Controller
{
    public function index()
    {
        $medicamentos = Medicamento::orderBy('nombre', 'asc')->get();
        return view('medicamento.index', compact('medicamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'             => 'required|string',
            'concentracion'      => 'required|string',
            'presentacion'       => 'required|string',
            'dosis'              => 'nullable|numeric',
            'via_administracion' => 'nullable|string',
            'frecuencia'         => 'nullable|string',
            'duracion'           => 'nullable|string',
            'cantidad_total'     => 'nullable|integer'
        ]);

        Medicamento::create($request->all());

        return redirect()->back()->with('success', 'Medicamento agregado al catálogo con sus valores por defecto.');
    }

    public function updateInline(Request $request, $id)
    {
        try {
            $medicamento = Medicamento::findOrFail($id);
            
            $medicamento->update($request->only([
                'nombre',
                'concentracion', 
                'presentacion',
                'dosis',
                'via_administracion',
                'frecuencia',
                'duracion',
                'cantidad_total'
            ]));
            
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $medicamento = Medicamento::findOrFail($id);
            $medicamento->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'success'], 200);
            }

            return redirect()->back()->with('success', 'Medicamento eliminado.');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'No se pudo eliminar.');
        }
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo_excel' => 'required|mimes:xlsx,xls'
        ]);

        try {
            // CORREGIDO: Ejecuta la importación y redirige limpiamente hacia atrás con la alerta verde
            Excel::import(new MedicamentosImport, $request->file('archivo_excel'));

            return redirect()->back()->with('success', 'Catálogo de medicamentos importado y actualizado exitosamente.');

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Ocurrió un error en la importación: ' . $e->getMessage());
        }
    }
}