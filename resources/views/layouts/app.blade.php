<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Superargo Consultas')</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #0a0a1a 0%, #1a1a3e 50%, #0d0d2b 100%);
            color: #e0e0e0;
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            background: rgba(15, 15, 40, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-brand {
            font-size: 1.2rem;
            font-weight: 700;
            color: #7c8aff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand span { color: #a78bfa; }

        .nav-links {
            display: flex;
            gap: 0.25rem;
            list-style: none;
        }

        .nav-links a {
            color: #b0b0d0;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .nav-links a:hover, .nav-links a.active {
            background: rgba(124, 138, 255, 0.15);
            color: #7c8aff;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.85rem;
            color: #888;
        }

        .nav-user .role-badge {
            background: rgba(124, 138, 255, 0.2);
            color: #7c8aff;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
        }

        .btn-logout {
            background: rgba(255, 80, 80, 0.15);
            color: #ff6b6b;
            border: 1px solid rgba(255, 80, 80, 0.3);
            padding: 0.35rem 0.8rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background: rgba(255, 80, 80, 0.3);
        }

        /* Container */
        .container {
            max-width: 1300px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        /* Glass card */
        .glass {
            background: rgba(20, 20, 50, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .glass-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .glass-header h2 {
            font-size: 1.2rem;
            color: #c0c0f0;
            font-weight: 600;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 1.2rem;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
        }

        .btn-primary:hover { opacity: 0.85; transform: translateY(-1px); }

        .btn-success {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: #fff;
        }

        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: #fff;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: #fff;
        }

        .btn-sm { padding: 0.35rem 0.8rem; font-size: 0.8rem; }

        /* Tables */
        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        th {
            background: rgba(124, 138, 255, 0.1);
            color: #a0a0d0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 0.75rem 0.6rem;
            text-align: left;
            white-space: nowrap;
        }

        td {
            padding: 0.65rem 0.6rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            color: #c0c0c0;
        }

        tr:hover td { background: rgba(124, 138, 255, 0.05); }

        /* Forms */
        .form-group { margin-bottom: 1rem; }

        .form-group label {
            display: block;
            margin-bottom: 0.3rem;
            font-size: 0.85rem;
            color: #a0a0c0;
        }

        .form-control {
            width: 100%;
            padding: 0.6rem 0.8rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            color: #e0e0e0;
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23888' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.8rem center;
            padding-right: 2rem;
        }

        select.form-control option { background: #1a1a3e; color: #e0e0e0; }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        /* Alerts */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.15);
            border: 1px solid rgba(46, 204, 113, 0.3);
            color: #2ecc71;
        }

        .alert-error {
            background: rgba(231, 76, 60, 0.15);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #e74c3c;
        }

        /* Progress bar */
        .progress-bar-wrap {
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            height: 24px;
            overflow: hidden;
            margin: 1rem 0;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 10px;
            transition: width 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            color: #fff;
            min-width: 40px;
        }

        /* Status badges */
        .badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-pending { background: rgba(241, 196, 15, 0.2); color: #f1c40f; }
        .badge-processing { background: rgba(52, 152, 219, 0.2); color: #3498db; }
        .badge-completed { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
        .badge-paused { background: rgba(230, 126, 34, 0.2); color: #e67e22; }
        .badge-found { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
        .badge-notfound { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }

        /* Pagination */
        .pagination {
            display: flex;
            gap: 0.3rem;
            justify-content: center;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .pagination a, .pagination span {
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.8rem;
            text-decoration: none;
            color: #a0a0d0;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
        }

        .pagination span.current {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            border-color: transparent;
        }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active { display: flex; }

        .modal {
            background: rgba(20, 20, 50, 0.95);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 1.5rem;
            width: 90%;
            max-width: 500px;
        }

        .modal h3 { margin-bottom: 1rem; color: #c0c0f0; }

        /* Result card (process view) */
        .result-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 10px;
            padding: 0.8rem 1rem;
            margin-bottom: 0.5rem;
            display: grid;
            grid-template-columns: 120px 1fr 1fr 100px;
            gap: 0.5rem;
            align-items: center;
            font-size: 0.82rem;
            animation: fadeIn 0.3s ease;
        }

        .result-card.found { border-left: 3px solid #2ecc71; }
        .result-card.notfound { border-left: 3px solid #e74c3c; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar { padding: 0 1rem; flex-wrap: wrap; height: auto; padding: 0.5rem 1rem; }
            .nav-links { flex-wrap: wrap; }
            .result-card { grid-template-columns: 1fr; }
            .form-row { grid-template-columns: 1fr; }
        }

        .text-muted { color: #666; }
        .text-center { text-align: center; }
        .mt-1 { margin-top: 0.5rem; }
        .mt-2 { margin-top: 1rem; }
        .mb-1 { margin-bottom: 0.5rem; }
        .flex { display: flex; }
        .gap-1 { gap: 0.5rem; }
        .items-center { align-items: center; }
    </style>
</head>
<body>
    @auth
    <nav class="navbar">
        <a href="/consultas" class="navbar-brand">⚕ Super<span>argo</span></a>
        <ul class="nav-links">
            <li><a href="{{ route('consultas.index') }}" class="{{ request()->routeIs('consultas.index') ? 'active' : '' }}">📋 Consultas</a></li>
            <li><a href="{{ route('consultas.search') }}" class="{{ request()->routeIs('consultas.search') ? 'active' : '' }}">🔍 Buscar</a></li>
            @if(auth()->user()->isAdmin())
            <li><a href="{{ route('consultas.files') }}" class="{{ request()->routeIs('consultas.files') ? 'active' : '' }}">📁 Archivos</a></li>
            <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.index') ? 'active' : '' }}">👥 Usuarios</a></li>
            @endif
        </ul>
        <div class="nav-user">
            <span>{{ auth()->user()->name }}</span>
            <span class="role-badge">{{ auth()->user()->role }}</span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="btn-logout">Salir</button>
            </form>
        </div>
    </nav>
    @endauth

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
</body>
</html>
