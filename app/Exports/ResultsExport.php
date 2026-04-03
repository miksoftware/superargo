<?php

namespace App\Exports;

use App\Models\Consulta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResultsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function __construct(private Consulta $consulta) {}

    public function headings(): array
    {
        return [
            'Cédula', 'Tipo Doc.', 'Primer Nombre', 'Segundo Nombre',
            'Primer Apellido', 'Segundo Apellido', 'Departamento', 'Municipio',
            'Dirección', 'Régimen', 'Población Especial', 'Grupo Étnico',
            'Paciente Riesgo', 'Otros Riesgos', 'Celular', 'Teléfono Fijo',
            'Correo', 'Estado Afiliado', 'Sede', 'IPS', 'EPS',
            'Fecha Nacimiento', 'Edad', 'Sexo', 'Encontrado', 'Error',
        ];
    }

    public function collection()
    {
        return $this->consulta->results()->where('processed', true)->get()->map(function ($r) {
            return [
                $r->cedula, $r->tipo_documento, $r->primer_nombre, $r->segundo_nombre,
                $r->primer_apellido, $r->segundo_apellido, $r->departamento, $r->municipio,
                $r->direccion, $r->regimen, $r->poblacion_especial, $r->grupo_etnico,
                $r->paciente_riesgo, $r->otros_riesgos, $r->celular, $r->telefono_fijo,
                $r->correo, $r->estado_afiliado, $r->sede, $r->ips, $r->eps_nombre,
                $r->fecha_nacimiento, $r->edad,
                $r->sexo == 1 ? 'Masculino' : ($r->sexo == 2 ? 'Femenino' : ''),
                $r->found ? 'Sí' : 'No', $r->error,
            ];
        });
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->consulta->results()->where('processed', true)->count() + 1;

        // Estilo del encabezado
        $sheet->getStyle('A1:Z1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1a1a2e'],
            ],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Bordes en todo el rango
        if ($lastRow > 1) {
            $sheet->getStyle("A1:Z{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '444444'],
                    ],
                ],
            ]);
        }

        return [];
    }
}
