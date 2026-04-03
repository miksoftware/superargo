@extends('layouts.app')
@section('title', 'Procesando')
@section('content')

<div class="glass">
    <div class="glass-header">
        <h2>⚡ Procesando: {{ $consulta->filename }}</h2>
        <div class="flex gap-1">
            <button id="btn-start" class="btn btn-success" onclick="startProcessing()" style="display:none">▶ Reanudar</button>
            <button id="btn-pause" class="btn btn-warning" onclick="pauseProcessing()">⏸ Pausar</button>
            <a href="{{ route('consultas.show', $consulta) }}" class="btn btn-primary">📋 Ver Resultados</a>
        </div>
    </div>

    <div style="display:flex;gap:2rem;flex-wrap:wrap;margin-bottom:1rem">
        <div>
            <span class="text-muted">Total:</span>
            <strong>{{ $consulta->total_cedulas }}</strong>
        </div>
        <div>
            <span class="text-muted">Procesadas:</span>
            <strong id="count-processed">{{ $consulta->processed }}</strong>
        </div>
        <div>
            <span class="text-muted">Encontradas:</span>
            <strong id="count-found" style="color:#2ecc71">0</strong>
        </div>
        <div>
            <span class="text-muted">No encontradas:</span>
            <strong id="count-notfound" style="color:#e74c3c">0</strong>
        </div>
        <div>
            <span class="text-muted">Estado:</span>
            <span id="status-text" class="badge badge-pending">Esperando</span>
        </div>
    </div>

    <div class="progress-bar-wrap">
        <div class="progress-bar-fill" id="progress-bar" style="width: {{ $consulta->total_cedulas > 0 ? ($consulta->processed / $consulta->total_cedulas * 100) : 0 }}%">
            <span id="progress-text">{{ $consulta->total_cedulas > 0 ? round($consulta->processed / $consulta->total_cedulas * 100) : 0 }}%</span>
        </div>
    </div>
</div>

<div class="glass">
    <h2 class="mb-1" style="font-size:1rem;color:#a0a0d0">Resultados en tiempo real</h2>
    <div id="results-container"></div>
    <p id="empty-msg" class="text-muted text-center" style="padding:1rem">Los resultados aparecerán aquí al iniciar el proceso.</p>
</div>

<script>
const consultaId = {{ $consulta->id }};
const total = {{ $consulta->total_cedulas }};
const delay = {{ config('superargo.delay', 500) }};
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

let running = false;
let found = 0;
let notfound = 0;
let processed = {{ $consulta->processed }};

function updateUI() {
    document.getElementById('count-processed').textContent = processed;
    document.getElementById('count-found').textContent = found;
    document.getElementById('count-notfound').textContent = notfound;

    const pct = total > 0 ? Math.round(processed / total * 100) : 0;
    document.getElementById('progress-bar').style.width = pct + '%';
    document.getElementById('progress-text').textContent = pct + '%';
}

function addResultCard(r) {
    document.getElementById('empty-msg').style.display = 'none';
    const container = document.getElementById('results-container');
    const div = document.createElement('div');
    div.className = 'result-card ' + (r.found ? 'found' : 'notfound');

    if (r.found) {
        div.innerHTML = `
            <div><strong>${r.cedula}</strong></div>
            <div>${r.primer_nombre || ''} ${r.segundo_nombre || ''} ${r.primer_apellido || ''} ${r.segundo_apellido || ''}</div>
            <div>${r.eps_nombre || ''} — ${r.municipio || ''}</div>
            <div><span class="badge badge-found">Encontrado</span></div>
        `;
    } else {
        div.innerHTML = `
            <div><strong>${r.cedula}</strong></div>
            <div colspan="2" style="color:#e74c3c">${r.error || 'No encontrado'}</div>
            <div><span class="badge badge-notfound">No encontrado</span></div>
        `;
    }

    container.prepend(div);
}

async function processNext() {
    if (!running) return;

    try {
        const res = await fetch(`/consultas/${consultaId}/process-next`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const data = await res.json();

        if (data.done) {
            running = false;
            document.getElementById('btn-start').style.display = 'inline-flex';
            document.getElementById('btn-start').textContent = '✅ Completado';
            document.getElementById('btn-start').disabled = true;
            document.getElementById('btn-pause').style.display = 'none';
            document.getElementById('status-text').textContent = 'Completado';
            document.getElementById('status-text').className = 'badge badge-completed';
            return;
        }

        processed = data.processed;
        if (data.result.found) found++; else notfound++;
        updateUI();
        addResultCard(data.result);

        // Delay antes de la siguiente
        setTimeout(processNext, delay);
    } catch (err) {
        console.error('Error:', err);
        document.getElementById('status-text').textContent = 'Error - Reintentar';
        document.getElementById('status-text').className = 'badge badge-paused';
        running = false;
        document.getElementById('btn-start').style.display = 'inline-flex';
        document.getElementById('btn-start').textContent = '🔄 Reintentar';
        document.getElementById('btn-pause').style.display = 'none';
    }
}

function startProcessing() {
    running = true;
    document.getElementById('btn-start').style.display = 'none';
    document.getElementById('btn-pause').style.display = 'inline-flex';
    document.getElementById('status-text').textContent = 'Procesando...';
    document.getElementById('status-text').className = 'badge badge-processing';
    processNext();
}

function pauseProcessing() {
    running = false;
    fetch(`/consultas/${consultaId}/pause`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    });
    document.getElementById('btn-start').style.display = 'inline-flex';
    document.getElementById('btn-start').textContent = '▶ Reanudar';
    document.getElementById('btn-pause').style.display = 'none';
    document.getElementById('status-text').textContent = 'Pausado';
    document.getElementById('status-text').className = 'badge badge-paused';
}

// Iniciar automáticamente al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    startProcessing();
});
</script>
@endsection
