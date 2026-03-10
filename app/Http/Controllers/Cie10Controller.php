<?php
namespace App\Http\Controllers;

use App\Models\Cie10;
use Illuminate\Http\Request;

class Cie10Controller extends Controller
{
    public function index() {
        $diagnosticos = Cie10::orderBy('codigo', 'asc')->get();
        return view('cie10.index', compact('diagnosticos'));
    }

    public function store(Request $request) {
        $request->validate([
            'codigo' => 'required',
            'descripcion' => 'required'
        ]);
        Cie10::create($request->all());
        return redirect()->back()->with('success', 'Diagnóstico agregado al catálogo.');
    }

    public function updateInline(Request $request, $id) {
        $cie = Cie10::findOrFail($id);
        $cie->update($request->only(['codigo', 'descripcion']));
        return response()->json(['status' => 'success']);
    }

    public function destroy($id) {
        Cie10::destroy($id);
        return response()->json(['status' => 'success']);
    }
}