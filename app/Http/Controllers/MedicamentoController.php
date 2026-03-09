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
        $request->validate([
            'nombre' => 'required|unique:medicamentos,nombre',
            'presentacion' => 'required'
        ]);

        Medicamento::create($request->all());

        // Redirigir atras para que el doctor no pierda lo que estaba escribiendo
        return redirect()->back()->with('success', 'Medicamento agregado al catálogo.');
    }

    public function updateInline(Request $request, $id)
    {
        try {
            $medicamento = Medicamento::findOrFail($id);
            $medicamento->update($request->only(['nombre', 'presentacion']));
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
