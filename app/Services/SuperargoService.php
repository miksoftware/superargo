<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SuperargoService
{
    private const DEPARTAMENTOS = [
        '5' => 'ANTIOQUIA', '8' => 'ATLÁNTICO', '11' => 'BOGOTÁ D.C.',
        '13' => 'BOLÍVAR', '15' => 'BOYACÁ', '17' => 'CALDAS',
        '18' => 'CAQUETÁ', '19' => 'CAUCA', '20' => 'CESAR',
        '23' => 'CÓRDOBA', '25' => 'CUNDINAMARCA', '27' => 'CHOCÓ',
        '41' => 'HUILA', '44' => 'LA GUAJIRA', '47' => 'MAGDALENA',
        '50' => 'META', '52' => 'NARIÑO', '54' => 'NORTE DE SANTANDER',
        '63' => 'QUINDÍO', '66' => 'RISARALDA', '68' => 'SANTANDER',
        '70' => 'SUCRE', '73' => 'TOLIMA', '76' => 'VALLE DEL CAUCA',
        '81' => 'ARAUCA', '85' => 'CASANARE', '86' => 'PUTUMAYO',
        '88' => 'SAN ANDRÉS Y PROVIDENCIA', '91' => 'AMAZONAS',
        '94' => 'GUAINÍA', '95' => 'GUAVIARE', '97' => 'VAUPÉS',
        '99' => 'VICHADA',
    ];

    private const EPS_NOMBRES = [
        // Régimen Contributivo
        'EPS001' => 'ALIANSALUD EPS', 'EPS002' => 'SALUD TOTAL EPS',
        'EPS003' => 'CAFESALUD EPS', 'EPS005' => 'SANITAS EPS',
        'EPS008' => 'COMPENSAR EPS', 'EPS010' => 'SURA EPS',
        'EPS012' => 'COMFENALCO VALLE EPS', 'EPS013' => 'SALUDCOOP EPS',
        'EPS016' => 'COOMEVA EPS', 'EPS017' => 'FAMISANAR EPS',
        'EPS018' => 'SERVICIO OCCIDENTAL DE SALUD SOS',
        'EPS023' => 'CRUZ BLANCA EPS', 'EPS033' => 'SALUDVIDA EPS',
        'EPS037' => 'NUEVA EPS', 'EPS039' => 'GOLDEN GROUP EPS',
        'EPS040' => 'MUTUAL SER EPS', 'EPS041' => 'COOSALUD EPS',
        'EPS042' => 'CAPITAL SALUD EPS', 'EPS044' => 'MEDIMÁS EPS',
        'EPS045' => 'EMSSANAR EPS', 'EPS046' => 'ASMET SALUD EPS',
        // Régimen Subsidiado (ESS / EPSS)
        'ESS002' => 'SALUD TOTAL', 'ESS024' => 'COOSALUD EPS-S',
        'ESS062' => 'ASMET SALUD', 'ESS076' => 'EMSSANAR EPS-S',
        'ESS091' => 'SAVIA SALUD EPS', 'ESS118' => 'MUTUAL SER',
        'ESS133' => 'SALUDVIDA EPS-S', 'ESS207' => 'NUEVA EPS-S',
        'EPSS33' => 'SALUDVIDA EPS-S', 'EPSS34' => 'CAJACOPI EPS-S',
        'EPSS37' => 'NUEVA EPS', 'EPSS40' => 'MUTUAL SER EPS-S',
        'EPSS41' => 'COOSALUD EPS-S', 'EPSS42' => 'CAPITAL SALUD EPS-S',
        'EPSS44' => 'MEDIMÁS EPS-S', 'EPSS45' => 'EMSSANAR EPS-S',
        'EPSS46' => 'ASMET SALUD EPS-S',
        // Cajas de compensación
        'CCF055' => 'COMFAMILIAR NARIÑO', 'CCF102' => 'COMFAMILIAR HUILA',
        'CCF053' => 'COMFAMILIAR CARTAGENA', 'CCF024' => 'COMFENALCO ANTIOQUIA',
        'CCF015' => 'COMFAMILIAR RISARALDA', 'CCF033' => 'COMFAMILIAR ATLÁNTICO',
        // Otros comunes
        'EAS016' => 'CONVIDA EPS-S', 'EAS027' => 'DUSAKAWI EPS-S',
        'RES001' => 'FUERZAS MILITARES', 'RES002' => 'POLICÍA NACIONAL',
        'RES003' => 'MAGISTERIO (FIDUPREVISORA)', 'RES004' => 'ECOPETROL',
        'RES005' => 'UNIVERSIDADES PÚBLICAS',
    ];

    // eps_tipo corresponde al tipo de entidad de Supersalud
    private const REGIMEN = [
        1 => 'ADMINISTRADORA DE RIESGOS LABORALES',
        2 => 'COMPAÑÍA DE SEGUROS',
        3 => 'MEDICINA PREPAGADA',
        4 => 'ENTIDAD ADAPTADA AL SISTEMA',
        5 => 'ENTIDAD TERRITORIAL',
        6 => 'RÉGIMEN CONTRIBUTIVO',
        7 => 'RÉGIMEN SUBSIDIADO',
        8 => 'RÉGIMEN DE EXCEPCIÓN Y ESPECIAL',
        9 => 'OTRO TIPO VIGILADO',
    ];

    private const TIPO_AFILIADO = [
        1 => 'Cotizante', 2 => 'Beneficiario', 3 => 'Adicional',
    ];

    public function consultar(string $cedula): array
    {
        try {
            $url = config('superargo.api_url') . $cedula;
            $timeout = config('superargo.timeout', 30);

            $response = Http::withoutVerifying()
                ->timeout($timeout)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->get($url);

            if (!$response->successful()) {
                return ['found' => false, 'error' => 'HTTP ' . $response->status()];
            }

            $data = $response->json();

            if (empty($data) || !isset($data['numero_doc'])) {
                return ['found' => false, 'error' => 'No encontrado en ADRES'];
            }

            return $this->mapResponse($data, $cedula);
        } catch (\Exception $e) {
            Log::error("Error consultando cédula {$cedula}: " . $e->getMessage());
            return ['found' => false, 'error' => $e->getMessage()];
        }
    }

    private function mapResponse(array $data, string $cedula): array
    {
        $nombres = $this->parseNombres($data);
        $municipio = $this->extractMunicipio($data);
        $deptId = (string) ($data['departamento_id'] ?? '');
        $epsCode = $data['eps'] ?? '';
        $epsTipo = $data['eps_tipo'] ?? null;

        return [
            'found' => true,
            'cedula' => $cedula,
            'tipo_documento' => 'Cédula de Ciudadanía',
            'primer_nombre' => $nombres['primer_nombre'],
            'segundo_nombre' => $nombres['segundo_nombre'],
            'primer_apellido' => $nombres['primer_apellido'],
            'segundo_apellido' => $nombres['segundo_apellido'],
            'departamento' => self::DEPARTAMENTOS[$deptId] ?? $deptId,
            'municipio' => $municipio,
            'direccion' => $data['direccion'] ?? '',
            'regimen' => self::REGIMEN[$epsTipo] ?? ($epsTipo ? "Tipo $epsTipo" : ''),
            'poblacion_especial' => '',
            'grupo_etnico' => '',
            'paciente_riesgo' => '',
            'otros_riesgos' => '',
            'celular' => $data['celular'] ?? '',
            'telefono_fijo' => $data['telefono'] ?? '',
            'correo' => $data['correo'] ?? '',
            'estado_afiliado' => $data['estado_afiliacion'] ?? '',
            'sede' => '',
            'ips' => '',
            'eps_nombre' => $this->resolveEpsName($epsCode, $data),
            'fecha_nacimiento' => $data['fecha_nacimiento'] ?? '',
            'edad' => !empty($data['edad']) ? (int) $data['edad'] : null,
            'sexo' => !empty($data['sexo']) ? (int) $data['sexo'] : null,
            'error' => null,
        ];
    }

    private function resolveEpsName(string $epsCode, array $data): string
    {
        // Primero buscar en la tabla de códigos conocidos
        if (isset(self::EPS_NOMBRES[$epsCode])) {
            return self::EPS_NOMBRES[$epsCode];
        }

        // Si no está, intentar extraer del campo estado_afiliacion: "AC en COOSALUD EPS-S municipio ..."
        $estado = $data['estado_afiliacion'] ?? '';
        if (preg_match('/en\s+(.+?)\s+municipio/i', $estado, $matches)) {
            return trim($matches[1]);
        }

        // Devolver el código tal cual si no se pudo resolver
        return $epsCode;
    }

    private function parseNombres(array $data): array
    {
        // Si la API trae campos separados
        if (!empty($data['nombre']) && !empty($data['apellido'])) {
            return [
                'primer_nombre' => trim($data['nombre'] ?? ''),
                'segundo_nombre' => trim($data['s_nombre'] ?? ''),
                'primer_apellido' => trim($data['apellido'] ?? ''),
                'segundo_apellido' => trim($data['s_apellido'] ?? ''),
            ];
        }

        // Si viene un solo campo "nombre_completo", parsearlo
        $full = $data['nombre_completo'] ?? $data['nombre'] ?? '';
        $parts = preg_split('/\s+/', trim($full));
        $count = count($parts);

        if ($count >= 4) {
            return [
                'primer_nombre' => $parts[0],
                'segundo_nombre' => $parts[1],
                'primer_apellido' => $parts[$count - 2],
                'segundo_apellido' => $parts[$count - 1],
            ];
        } elseif ($count === 3) {
            return [
                'primer_nombre' => $parts[0],
                'segundo_nombre' => '',
                'primer_apellido' => $parts[1],
                'segundo_apellido' => $parts[2],
            ];
        } elseif ($count === 2) {
            return [
                'primer_nombre' => $parts[0],
                'segundo_nombre' => '',
                'primer_apellido' => $parts[1],
                'segundo_apellido' => '',
            ];
        }

        return [
            'primer_nombre' => $full,
            'segundo_nombre' => '',
            'primer_apellido' => '',
            'segundo_apellido' => '',
        ];
    }

    private function extractMunicipio(array $data): string
    {
        // Extraer nombre del municipio desde estado_afiliacion: "AC en NUEVA EPS municipio BUCARAMANGA"
        $estado = $data['estado_afiliacion'] ?? '';
        if (preg_match('/municipio\s+(.+)$/i', $estado, $matches)) {
            return trim($matches[1]);
        }
        return $data['municipio_id'] ?? '';
    }
}
