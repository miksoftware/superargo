@extends('layouts.app')
@section('title', 'Archivos Exportables')
@section('content')

<div class="glass">
    <div class="glass-header">
        <h2>📁 Archivos Exportables</h2>
    </div>

    @if($consultas->isEmpty())
        <p class="text-muted text-center" style="padding:2rem">No hay consultas completadas para exportar.</p>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Archivo Original</th>
                    <th>Total Cédulas</th>
                    <th>Encontradas</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consultas as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->filename }}</td>
                    <td>{{ $c->total_cedulas }}</td>
                    <td>{{ $c->found_count }}</td>
                    <td>{{ $c->created_at->format('d/m/Y H:i') }}</td>
                    <td class="flex gap-1">
                        <a href="{{ route('consultas.export', $c) }}" class="btn btn-warning btn-sm">📥 Descargar Excel</a>
                        <a href="{{ route('consultas.show', $c) }}" class="btn btn-primary btn-sm">Ver</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
