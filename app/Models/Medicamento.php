<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $fillable = [
        'nombre', 
        'concentracion', 
        'presentacion',
        'dosis',
        'via_administracion',
        'frecuencia',
        'duracion',
        'cantidad_total'
    ];
}
