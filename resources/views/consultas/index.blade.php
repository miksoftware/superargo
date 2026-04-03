@extends('layouts.app')
@section('title', 'Consultas')
@section('content')

@if(auth()->user()->isAdmin())
<div class="glass">
    <div class="glass-header">
        <h2>📤 Subir Archivo de Cédulas</h2>
    </div>
    <form method="POST" action="{{ route('consultas.upload') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
            <div class="form-group" style="flex:1">
                <label>Archivo Excel o CSV (una cédula por fila)</label>
                <input type="file" name="archivo" class="form-control" accept=".xlsx,.xls,.csv" required>
            </div>
            <div class="form-group" style="display:flex;align-items:flex-end">
                <button type="submit" class="btn btn-primary">📤 Subir y Procesar</button>
            </div>
        </div>
    </form>
</div>
@endif

<div class="glass">
    <div class="glass-header">
        <h2>📋 Historial de Consultas</h2>
    </div>

    @if($consultas->isEmpty())
        <p class="text-muted text-center" style="padding:2rem">No hay consultas registradas.</p>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Archivo</th>
                    <th>Usuario</th>
                    <th>Cédulas</th>
                    <th>Procesadas</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consultas as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->filename }}</td>
                    <td>{{ $c->user->name ?? 'N/A' }}</td>
                    <td>{{ $c->total_cedulas }}</td>
                    <td>{{ $c->processed }} / {{ $c->total_cedulas }}</td>
                    <td>
                        <span class="badge badge-{{ $c->status }}">{{ ucfirst($c->status) }}</span>
                    </td>
                    <td>{{ $c->created_at->format('d/m/Y H:i') }}</td>
                    <td class="flex gap-1">
                        <a href="{{ route('consultas.show', $c) }}" class="btn btn-primary btn-sm">Ver</a>
                        @if(auth()->user()->isAdmin())
                            @if(in_array($c->status, ['pending', 'paused', 'processing']))
                                <a href="{{ route('consultas.process', $c) }}" class="btn btn-success btn-sm">▶ Reanudar</a>
                            @endif
                            @if($c->status === 'completed')
                                <a href="{{ route('consultas.export', $c) }}" class="btn btn-warning btn-sm">📥 Excel</a>
                            @endif
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pagination">
        @if($consultas->onFirstPage())
            <span class="text-muted">← Anterior</span>
        @else
            <a href="{{ $consultas->previousPageUrl() }}">← Anterior</a>
        @endif

        @foreach($consultas->getUrlRange(1, $consultas->lastPage()) as $page => $url)
            @if($page == $consultas->currentPage())
                <span class="current">{{ $page }}</span>
            @else
                <a href="{{ $url }}">{{ $page }}</a>
            @endif
        @endforeach

        @if($consultas->hasMorePages())
            <a href="{{ $consultas->nextPageUrl() }}">Siguiente →</a>
        @else
            <span class="text-muted">Siguiente →</span>
        @endif
    </div>
    @endif
</div>
@endsection
