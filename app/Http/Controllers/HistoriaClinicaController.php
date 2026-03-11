<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Medicamento;
use App\Models\Antecedente;
use App\Models\HistoriaClinica;
use App\Models\DiagnosticoAtencion;
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
                    ->with(['cita.recetas', 'diagnosticos']) // Cargamos diagnósticos previos
                    ->orderBy('created_at', 'desc')
                    ->get();

        $medicamentosLista = Medicamento::all();

        return view('historias.create', compact('cita', 'antecedentes', 'historiasAnteriores', 'medicamentosLista','cie10Lista'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cita_id' => 'required',
            'anamnesis' => 'required',
        ]);

        // 1. Guardar o actualizar la base de la Historia Clínica
        $historia = HistoriaClinica::updateOrCreate(
            ['cita_id' => $request->cita_id],
            [
                'paciente_id'   => $request->paciente_id,
                'anamnesis'     => $request->anamnesis,
                'examen_fisico' => $request->examen_fisico,
                'plan'          => $request->plan,
            ]
        );

        // 2. Sincronizar Múltiples Diagnósticos
        if ($request->has('diagnosticos')) {
            // Limpiamos los anteriores para esta historia específica
            DiagnosticoAtencion::where('historia_clinica_id', $historia->id)->delete();
            
            foreach ($request->diagnosticos as $d) {
                $historia->diagnosticos()->create([
                    'cie_10'      => $d['cie_10'],
                    'diagnostico' => $d['diagnostico']
                ]);
            }
        }

        // 3. Guardar las recetas
        if ($request->has('recetas')) {
            RecetaController::guardarRecetas($request->cita_id, $request->recetas);
        }

        Cita::where('id', $request->cita_id)->update(['estado' => 'Atendido']);

        return redirect()->route('dashboard')->with([
            'success' => 'Atención médica registrada correctamente.',
            'imprimir_receta' => route('receta.pdf', $request->cita_id)
        ]);
    }

    public function edit($id)
    {
        // Eager loading de diagnósticos para que aparezcan en la vista de edición
        $historia = HistoriaClinica::with(['cita.paciente', 'diagnosticos'])->findOrFail($id);
        
        $cita = $historia->cita;
        $paciente = $cita->paciente;

        $medicamentosLista = Medicamento::all();
        $cie10Lista = \App\Models\Cie10::all();
        $antecedentes = Antecedente::where('paciente_id', $paciente->id)->get();

        $historiasAnteriores = HistoriaClinica::where('paciente_id', $paciente->id)
                                ->where('id', '!=', $id)
                                ->with(['diagnosticos'])
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
        ]);

        $historia = HistoriaClinica::findOrFail($id);
        $historia->update($request->only(['anamnesis', 'examen_fisico', 'plan']));

        // Actualizar diagnósticos (Borrar e insertar nuevos)
        if ($request->has('diagnosticos')) {
            $historia->diagnosticos()->delete();
            foreach ($request->diagnosticos as $d) {
                $historia->diagnosticos()->create([
                    'cie_10'      => $d['cie_10'],
                    'diagnostico' => $d['diagnostico']
                ]);
            }
        }

        if ($request->has('recetas')) {
            RecetaController::guardarRecetas($historia->cita_id, $request->recetas);
        }

        return redirect()->route('dashboard')->with([
            'success' => 'Cambios guardados correctamente.',
            'imprimir_receta' => route('receta.pdf', $historia->cita_id)
        ]);
    }

    public function autoguardar(Request $request)
    {
        $historia = HistoriaClinica::updateOrCreate(
            ['cita_id' => $request->cita_id],
            [
                'paciente_id'   => $request->paciente_id,
                'anamnesis'     => $request->anamnesis,
                'examen_fisico' => $request->examen_fisico,
                'plan'          => $request->plan,
            ]
        );

        if ($request->has('diagnosticos')) {
            $historia->diagnosticos()->delete();
            foreach ($request->diagnosticos as $d) {
                $historia->diagnosticos()->create([
                    'cie_10'      => $d['cie_10'],
                    'diagnostico' => $d['diagnostico']
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}