<?php

namespace App\Http\Controllers;

use App\Models\Receta;
use App\Models\Cita; // IMPORTANTE
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // IMPORTANTE para el PDF

class RecetaController extends Controller
{
    public static function guardarRecetas($cita_id, $recetasArray)
    {
        if (!$recetasArray || !is_array($recetasArray)) return;

        // Limpiamos recetas previas para evitar duplicados si se re-edita
        Receta::where('cita_id', $cita_id)->delete();

        foreach ($recetasArray as $datos) {
            Receta::create([
                'cita_id'            => $cita_id,
                'medicamento'        => $datos['medicamento'],
                'presentacion'       => $datos['presentacion'] ?? 'N/A',
                'dosis'              => $datos['dosis'],
                'via_administracion' => $datos['via_administracion'],
                'frecuencia'         => $datos['frecuencia'],
                'duracion'           => $datos['duracion'],
                'cantidad_total'     => $datos['cantidad_total'],
            ]);
        }
    }

    public function generarPDF($cita_id)
    {
        $cita = Cita::with(['paciente', 'recetas', 'historiaClinica'])->findOrFail($cita_id);
        
        $data = [
            'paciente'    => $cita->paciente->apellido . ', ' . $cita->paciente->nombre,
            'dni'         => $cita->paciente->dni,
            'diagnostico' => $cita->historiaClinica->diagnostico ?? 'Ver historia',
            'edad'        => \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->age,
            'fecha'       => date('d/m/Y'),
            'recetas'     => $cita->recetas
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.receta', $data);
        
        // Configuramos el papel A4
        return $pdf->setPaper('a4', 'portrait')->stream('receta_medica.pdf');
    }
}