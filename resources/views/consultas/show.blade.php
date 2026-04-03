@extends('layouts.app')
@section('title', 'Detalle Consulta #' . $consulta->id)
@section('content')

<div class="glass">
    <div class="glass-header">
        <h2>📋 Consulta #{{ $consulta->id }} — {{ $consulta->filename }}</h2>
        <div class="flex gap-1">
            @if(auth()->user()->isAdmin() && $consulta->status === 'completed')
                <a href="{{ route('consultas.export', $consulta) }}" class="btn btn-warning btn-sm">📥 Exportar Excel</a>
            @endif
            <a href="{{ route('consultas.index') }}" class="btn btn-primary btn-sm">← Volver</a>
        </div>
    </div>

    <div style="display:flex;gap:2rem;flex-wrap:wrap;margin-bottom:1rem;font-size:0.85rem">
        <div><span class="text-muted">Usuario:</span> {{ $consulta->user->name ?? 'N/A' }}</div>
        <div><span class="text-muted">Total:</span> {{ $consulta->total_cedulas }}</div>
        <div><span class="text-muted">Procesadas:</span> {{ $consulta->processed }}</div>
        <div><span class="text-muted">Estado:</span> <span class="badge badge-{{ $consulta->status }}">{{ ucfirst($consulta->status) }}</span></div>
        <div><span class="text-muted">Fecha:</span> {{ $consulta->created_at->format('d/m/Y H:i') }}</div>
    </div>
</div>

<div class="glass">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Cédula</th>
                    <th>Nombre Completo</th>
                    <th>EPS</th>
                    <th>Departamento</th>
                    <th>Municipio</th>
                    <th>Régimen</th>
                    <th>Estado</th>
                    <th>Encontrado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $r)
                <tr>
                    <td>{{ $r->cedula }}</td>
                    <td>{{ $r->primer_nombre }} {{ $r->segundo_nombre }} {{ $r->primer_apellido }} {{ $r->segundo_apellido }}</td>
                    <td>{{ $r->eps_nombre }}</td>
                    <td>{{ $r->departamento }}</td>
                    <td>{{ $r->municipio }}</td>
                    <td>{{ $r->regimen }}</td>
                    <td style="font-size:0.78rem">{{ $r->estado_afiliado }}</td>
                    <td>
                        @if($r->found)
                            <span class="badge badge-found">Sí</span>
                        @else
                            <span class="badge badge-notfound">No</span>
                            @if($r->error)<br><small class="text-muted">{{ $r->error }}</small>@endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted" style="padding:2rem">Sin resultados procesados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($results->hasPages())
    <div class="pagination">
        @if($results->onFirstPage())
            <span class="text-muted">← Anterior</span>
        @else
            <a href="{{ $results->previousPageUrl() }}">← Anterior</a>
        @endif

        @foreach($results->getUrlRange(1, $results->lastPage()) as $page => $url)
            @if($page == $results->currentPage())
                <span class="current">{{ $page }}</span>
            @else
                <a href="{{ $url }}">{{ $page }}</a>
            @endif
        @endforeach

        @if($results->hasMorePages())
            <a href="{{ $results->nextPageUrl() }}">Siguiente →</a>
        @else
            <span class="text-muted">Siguiente →</span>
        @endif
    </div>
    @endif
</div>
@endsection
