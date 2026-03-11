<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriaClinica extends Model
{
    // Nombre de la tabla
    protected $table = 'historias_clinicas';

    // CAMPOS AUTORIZADOS PARA GUARDAR
    protected $fillable = [
        'paciente_id',
        'cita_id',
        'anamnesis',
        'examen_fisico',
        'diagnostico',
        'cie_10',
        'plan'
    ];

    public function cita() {
        return $this->belongsTo(Cita::class, 'cita_id');
    }
    public function diagnosticos() {
    return $this->hasMany(DiagnosticoAtencion::class, 'historia_clinica_id');
    }
}
