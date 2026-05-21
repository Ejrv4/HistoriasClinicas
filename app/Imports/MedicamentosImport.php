<?php

namespace App\Imports;

use App\Models\Medicamento;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class MedicamentosImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $rowClean = [];
        foreach ($row as $key => $value) {
            $keyClean = Str::slug(str_replace('_', ' ', $key), '_');
            $rowClean[$keyClean] = $value;
        }

        if (empty($rowClean['medicamento']) || empty($rowClean['presentacion'])) {
            return null;
        }

        // 1. FRECUENCIA
        $frecuencia = isset($rowClean['frecuencia']) ? trim($rowClean['frecuencia']) : '';
        if ($frecuencia && Str::contains(Str::lower($frecuencia), ['unic', 'únic'])) {
            $frecuencia = 'Dosis Única';
        }

        // 2. DURACIÓN
        $duracion = isset($rowClean['duracion']) ? trim($rowClean['duracion']) : '';
        if ($frecuencia === 'Dosis Única') {
            $duracion = ''; 
        }

        // 3. PRESENTACIÓN Y VÍA
        $presentacion = trim(strtoupper($rowClean['presentacion']));
        $presentacion = str_replace(['CAPSULA', 'APLICACION'], ['CÁPSULA', 'APLICACIÓN'], $presentacion);
        
        $via = isset($rowClean['via_administracion']) ? trim(strtoupper($rowClean['via_administracion'])) : '';

        // 4. CONCENTRACIÓN
        $concentracion = isset($rowClean['concentracion']) && trim($rowClean['concentracion']) !== '' 
            ? trim(strtoupper($rowClean['concentracion'])) 
            : '';

        // 5. DOSIS Y TOTAL
        $dosis = isset($rowClean['dosis']) && is_numeric($rowClean['dosis']) ? (float)$rowClean['dosis'] : null;
        $cantidadTotal = isset($rowClean['cantidad_total']) && is_numeric($rowClean['cantidad_total']) ? (int)$rowClean['cantidad_total'] : null;
        
        if ($frecuencia === 'Dosis Única' && !$cantidadTotal && $dosis) {
            $cantidadTotal = ceil($dosis);
        }

        // BLOQUEO TOTAL DE SOBREESCRITURAS: Comparamos por todos los campos clínicos posibles.
        // Si difieren en Vía (Tópica vs Rectal) o Duración, se creará un registro único e independiente.
        return Medicamento::updateOrCreate(
            [
                'nombre'             => trim($rowClean['medicamento']),
                'concentracion'      => $concentracion, 
                'presentacion'       => $presentacion,
                'via_administracion' => $via,
                'frecuencia'         => $frecuencia,
                'duracion'           => $duracion
            ],
            [
                'dosis'              => $dosis,
                'cantidad_total'     => $cantidadTotal,
            ]
        );
    }
}