<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Antecedente;

class AntecedenteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'tipo' => 'required|string',
            'descripcion' => 'required|string',
        ]);

        \App\Models\Antecedente::create($request->all());

        // Regresa a la página anterior pero añade un fragmento a la URL 
        // para que el navegador se mantenga en la pestaña de antecedentes
        return redirect()->to(url()->previous() . '#pestana-antecedentes')
                        ->with('success', 'Antecedente actualizado.');
    }

    public function guardarTodo(Request $request)
{
    try {
        $paciente_id = $request->paciente_id;
        
        // MAPEADO CORREGIDO: Usamos términos cortos para no desbordar la columna
        $mapeo = [
            'Medico'      => 'Médico',    
            'Quirúrgico'  => 'Quirúrgico', 
            'Alergia'     => 'Alergia',     
            'Medicación'  => 'Medicación'   
        ];

        foreach ($mapeo as $input => $tipoBD) {
            // Solo guardamos si hay texto o para limpiar
            \App\Models\Antecedente::updateOrCreate(
                [
                    'paciente_id' => $paciente_id, 
                    'tipo' => $tipoBD
                ],
                [
                    'descripcion' => $request->input($input) ?? '' // Aseguramos capturar el valor
                ]
            );
        }

        return response()->json(['status' => 'success'], 200);
    } catch (\Exception $e) {
        // Esto nos dirá exactamente qué falló si vuelve a ocurrir
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}
}
