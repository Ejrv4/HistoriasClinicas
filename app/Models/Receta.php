<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    protected $fillable = [
        'cita_id', 'medicamento', 'presentacion', 'dosis', 
        'via_administracion', 'frecuencia', 'duracion','cantidad_total'
    ];

    public function cita() {
        return $this->belongsTo(Cita::class);
    }
}
