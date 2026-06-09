<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'pais_nacimiento',
        'ignorar_alerta'
    ];

    public function historiasClinicas(): HasMany
    {
        return $this->hasMany(HistoriaClinica::class, 'paciente_id');
    }
}