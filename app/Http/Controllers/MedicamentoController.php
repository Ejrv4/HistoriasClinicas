<?php

namespace App\Http\Controllers;

use App\Models\Medicamento;
use Illuminate\Http\Request;

class MedicamentoController extends Controller
{
    public function index()
    {
        $medicamentos = Medicamento::orderBy('nombre', 'asc')->get();
        return view('medicamento.index', compact('medicamentos'));
    }

    public function store(Request $request)
    {
        // Validamos los campos requeridos base y dejamos los nuevos como opcionales
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

        // Al usar $request->all(), Laravel tomará automáticamente los inputs hidden 
        // concatendados por JS ('frecuencia' y 'duracion') junto con el resto
        Medicamento::create($request->all());

        return redirect()->back()->with('success', 'Medicamento agregado al catálogo con sus valores por defecto.');
    }

    public function updateInline(Request $request, $id)
    {
        try {
            $medicamento = Medicamento::findOrFail($id);
            
            // ACTUALIZACIÓN: Añadimos los 5 nuevos campos al array de captura permitida
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
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}