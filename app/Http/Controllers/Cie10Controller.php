<?php
namespace App\Http\Controllers;

use App\Models\Cie10;
use Illuminate\Http\Request;
use App\Imports\Cie10Import;
use Maatwebsite\Excel\Facades\Excel;

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

    public function importar(Request $request)
{
    // Validar que el archivo sea enviado y que tenga el formato correcto
    $request->validate([
        'archivo_excel' => 'required|mimes:xlsx,xls'
    ]);

    try {
        Excel::import(new Cie10Import, $request->file('archivo_excel'));

        return redirect()->route('cie10.index')->with('success', 'Catálogo CIE-10 importado y actualizado exitosamente.');
    } catch (\Exception $e) {
        return redirect()->route('cie10.index')->with('error', 'Ocurrió un error en la importación. Verifique el formato del archivo.');
    }
}
}