<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'citas';
    protected $fillable = ['paciente_id', 'fecha', 'hora', 'motivo', 'estado'];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function recetas() {
        return $this->hasMany(Receta::class, 'cita_id');
    }

    public function historiaClinica() {
        return $this->hasOne(HistoriaClinica::class, 'cita_id');
    }
}


