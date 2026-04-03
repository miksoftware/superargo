<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CedulasImport implements ToCollection
{
    public Collection $cedulas;

    public function __construct()
    {
        $this->cedulas = collect();
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $val = trim((string) ($row[0] ?? ''));
            // Limpiar: quitar puntos, comas, espacios, dejar solo dígitos
            $val = preg_replace('/[^0-9]/', '', $val);
            if (!empty($val) && strlen($val) >= 5 && $val !== '0') {
                $this->cedulas->push($val);
            }
        }
        // Quitar duplicados
        $this->cedulas = $this->cedulas->unique()->values();
    }
}
