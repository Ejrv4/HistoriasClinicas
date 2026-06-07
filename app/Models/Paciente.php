<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table = 'pacientes';
    
    protected $fillable = [
        'dni',
        'trabajo',
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
            'nombre'           => 'required|string|max:255',
            'apellido'         => 'required|string|max:255',

            'dni'              => 'nullable|unique:pacientes,dni',
            'trabajo'          => 'nullable|string', 
            'fecha_nacimiento' => 'nullable|date',
            'genero'           => 'nullable|string',
            'celular_personal' => 'nullable|string',
            'correo'           => 'nullable|email',
            'distrito'         => 'nullable|string',
            'direccion'        => 'nullable|string',
            'pais_nacimiento'  => 'nullable|string'
        ], [
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
