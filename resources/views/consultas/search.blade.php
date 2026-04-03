@extends('layouts.app')
@section('title', 'Buscar por Cédula')
@section('content')

<div class="glass">
    <div class="glass-header">
        <h2>🔍 Buscar Afiliado por Cédula</h2>
    </div>

    <form method="GET" action="{{ route('consultas.search') }}">
        <div class="form-row">
            <div class="form-group" style="flex:1">
                <label>Número de cédula</label>
                <input type="text" name="cedula" class="form-control" value="{{ $cedula ?? '' }}" placeholder="Ej: 1003812935" required>
            </div>
            <div class="form-group" style="display:flex;align-items:flex-end">
                <button type="submit" class="btn btn-primary">🔍 Buscar</button>
            </div>
        </div>
    </form>
</div>

@if(isset($results))
<div class="glass">
    @if($results && $results->isNotEmpty())
        <h2 style="font-size:1rem;color:#a0a0d0;margin-bottom:1rem">Se encontraron {{ $results->count() }} resultado(s) para cédula {{ $cedula }}</h2>

        @foreach($results as $r)
        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:12px;padding:1.2rem;margin-bottom:1rem">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.8rem">
                <div>
                    <strong style="font-size:1.1rem;color:#c0c0f0">{{ $r->primer_nombre }} {{ $r->segundo_nombre }} {{ $r->primer_apellido }} {{ $r->segundo_apellido }}</strong>
                    <span class="text-muted" style="margin-left:0.5rem">CC {{ $r->cedula }}</span>
                </div>
                <span class="badge badge-found">Encontrado</span>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:0.6rem;font-size:0.85rem">
                <div><span class="text-muted">EPS:</span> {{ $r->eps_nombre }}</div>
                <div><span class="text-muted">Régimen:</span> {{ $r->regimen }}</div>
                <div><span class="text-muted">Departamento:</span> {{ $r->departamento }}</div>
                <div><span class="text-muted">Municipio:</span> {{ $r->municipio }}</div>
                <div><span class="text-muted">Dirección:</span> {{ $r->direccion ?: '—' }}</div>
                <div><span class="text-muted">Celular:</span> {{ $r->celular ?: '—' }}</div>
                <div><span class="text-muted">Teléfono:</span> {{ $r->telefono_fijo ?: '—' }}</div>
                <div><span class="text-muted">Correo:</span> {{ $r->correo ?: '—' }}</div>
                <div><span class="text-muted">Fecha Nac.:</span> {{ $r->fecha_nacimiento ?: '—' }}</div>
                <div><span class="text-muted">Edad:</span> {{ $r->edad ?? '—' }}</div>
                <div><span class="text-muted">Sexo:</span> {{ $r->sexo == 1 ? 'Masculino' : ($r->sexo == 2 ? 'Femenino' : '—') }}</div>
                <div><span class="text-muted">Estado:</span> {{ $r->estado_afiliado }}</div>
                <div><span class="text-muted">Sede:</span> {{ $r->sede ?: '—' }}</div>
                <div><span class="text-muted">IPS:</span> {{ $r->ips ?: '—' }}</div>
                <div><span class="text-muted">Consulta:</span> #{{ $r->consulta->id ?? '' }} — {{ $r->consulta->created_at->format('d/m/Y') ?? '' }}</div>
            </div>
        </div>
        @endforeach
    @else
        <p class="text-center text-muted" style="padding:2rem">No se encontraron resultados para la cédula <strong>{{ $cedula }}</strong>.</p>
    @endif
</div>
@endif
@endsection
