<?php

namespace App\Imports;

use App\Models\Cie10; // Asegúrate de que tu modelo se llame así
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Cie10Import implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Si la fila viene vacía, la ignoramos
        if (!isset($row['codigo']) || !isset($row['diagnostico'])) {
            return null;
        }

        // updateOrCreate busca por código; si existe actualiza la descripción, si no, lo crea.
        return Cie10::updateOrCreate(
            ['codigo' => trim($row['codigo'])],
            ['descripcion' => trim($row['diagnostico'])]
        );
    }
}