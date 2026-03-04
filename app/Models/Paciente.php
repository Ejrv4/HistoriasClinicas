<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table = 'pacientes';
    
    protected $fillable = [
        'dni',
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'genero',
        'celular_personal',
        'correo', 
        'distrito',
        'direccion',
        'pais_nacimiento'
    ];

    public function store(Request $request)
    {
        $request->validate([
            // 'unique:tabla,columna'
            'dni' => 'required|unique:pacientes,dni', 
            'nombre' => 'required',
            'apellido' => 'required',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required',
            'celular_personal' => 'required',
            'correo' => 'nullable|email',
            'distrito' => 'required',
            'direccion' => 'required',
            'pais_nacimiento' => 'required'
        ], [
            // Mensaje personalizado (opcional)
            'dni.unique' => 'Este DNI ya se encuentra registrado en el sistema.',
        ]);

        $paciente = \App\Models\Paciente::create($request->all());

        if ($request->has('crear_cita_ahora')) {
            return redirect()->route('citas.create', [
                'paciente_id' => $paciente->id,
                'quick_start' => true
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Paciente registrado correctamente.');
    }
}
