<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosticoAtencion extends Model
{
    protected $table = 'diagnosticos_atencion';

    protected $fillable = [
        'historia_clinica_id', 
        'cie_10', 
        'diagnostico'
    ];

    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class, 'historia_clinica_id');
    }
}
