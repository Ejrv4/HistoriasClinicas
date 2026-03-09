<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Medicamento;
use App\Models\Antecedente;
use App\Models\HistoriaClinica;
use App\Http\Controllers\RecetaController; 

class HistoriaClinicaController extends Controller
{   
    public function create(Request $request)
    {
        $cita = Cita::with('paciente')->findOrFail($request->cita_id);
        $antecedentes = Antecedente::where('paciente_id', $cita->paciente_id)->get();
        $cie10Lista = \App\Models\Cie10::all();
        
        $historiasAnteriores = HistoriaClinica::where('paciente_id', $cita->paciente_id)
                    ->where('cita_id', '!=', $cita->id)
                    ->with(['cita.recetas'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        $medicamentosLista = Medicamento::all();

        return view('historias.create', compact('cita', 'antecedentes', 'historiasAnteriores', 'medicamentosLista','cie10Lista'));
    }

    public function store(Request $request)
    {
        // 1. Validación de campos obligatorios
        $request->validate([
            'cita_id' => 'required',
            'anamnesis' => 'required',
            'diagnostico' => 'required',
        ]);

        // 2. Guardar o actualizar la Historia Clínica
        // Usamos updateOrCreate por si el autoguardado ya generó el registro
        HistoriaClinica::updateOrCreate(
            ['cita_id' => $request->cita_id],
            [
                'paciente_id'   => $request->paciente_id,
                'anamnesis'     => $request->anamnesis,
                'examen_fisico' => $request->examen_fisico,
                'diagnostico'   => $request->diagnostico,
                'cie_10'        => $request->cie_10,
                'plan'          => $request->plan,
            ]
        );

        // 3. Guardar las recetas (llamada al método estático)
        if ($request->has('recetas')) {
            RecetaController::guardarRecetas($request->cita_id, $request->recetas);
        }

        // 4. Actualizar el estado de la cita a 'Atendido'
        Cita::where('id', $request->cita_id)->update(['estado' => 'Atendido']);

        // 5. Redireccionamiento al Dashboard con la URL del PDF para el script
        return redirect()->route('dashboard')->with([
            'success' => 'Historia Clínica guardada correctamente.',
            'imprimir_receta' => route('receta.pdf', $request->cita_id)
        ]);
    }

    public function edit($id)
    {
        $historia = HistoriaClinica::with('cita.paciente')->findOrFail($id);
        
        $cita = $historia->cita;
        $paciente = $cita->paciente;

        $medicamentosLista = Medicamento::all();
        $cie10Lista = \App\Models\Cie10::all();
        $antecedentes = Antecedente::where('paciente_id', $paciente->id)->get();

        $historiasAnteriores = HistoriaClinica::where('paciente_id', $paciente->id)
                                ->where('id', '!=', $id)
                                ->orderBy('created_at', 'desc')
                                ->get();

        return view('historias.edit', compact(
            'historia', 
            'cita', 
            'paciente', 
            'medicamentosLista', 
            'antecedentes',
            'cie10Lista', 
            'historiasAnteriores'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'anamnesis' => 'required',
            'diagnostico' => 'required',
        ]);

        $historia = HistoriaClinica::findOrFail($id);
        $historia->update($request->only(['anamnesis', 'examen_fisico', 'diagnostico', 'cie_10', 'plan']));

        // Ya no borramos aqui, porque guardarRecetas ya lo hace internamente
        if ($request->has('recetas')) {
            RecetaController::guardarRecetas($historia->cita_id, $request->recetas);
        }

        return redirect()->route('dashboard')->with([
            'success' => 'Cambios guardados.',
            'imprimir_receta' => route('receta.pdf', $historia->cita_id)
        ]);
    }

    public function autoguardar(Request $request)
    {
        HistoriaClinica::updateOrCreate(
            ['cita_id' => $request->cita_id],
            [
                'paciente_id'   => $request->paciente_id,
                'anamnesis'     => $request->anamnesis,
                'examen_fisico' => $request->examen_fisico,
                'diagnostico'   => $request->diagnostico,
                'cie_10'        => $request->cie_10,
                'plan'          => $request->plan,
            ]
        );

        return response()->json(['status' => 'success']);
    }
}