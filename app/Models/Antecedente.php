<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antecedente extends Model
{
    protected $fillable = ['paciente_id', 'tipo', 'descripcion'];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }
}
