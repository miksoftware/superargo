@extends('layouts.app')
@section('title', 'Usuarios')
@section('content')

<div class="glass">
    <div class="glass-header">
        <h2>👥 Gestión de Usuarios</h2>
        <button class="btn btn-primary" onclick="document.getElementById('modal-create').classList.add('active')">+ Nuevo Usuario</button>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Creado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge {{ $user->role === 'admin' ? 'badge-processing' : 'badge-pending' }}">{{ $user->role }}</span></td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="flex gap-1">
                        <button class="btn btn-warning btn-sm" onclick="editUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}')">Editar</button>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('¿Eliminar este usuario?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal-overlay" id="modal-create">
    <div class="modal">
        <h3>Nuevo Usuario</h3>
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <div class="form-group">
                <label>Rol</label>
                <select name="role" class="form-control">
                    <option value="consulta">Consulta</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="flex gap-1">
                <button type="submit" class="btn btn-primary">Crear</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('modal-create').classList.remove('active')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal-overlay" id="modal-edit">
    <div class="modal">
        <h3>Editar Usuario</h3>
        <form method="POST" id="form-edit">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="name" id="edit-name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="edit-email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Nueva contraseña (dejar vacío para no cambiar)</label>
                <input type="password" name="password" class="form-control" minlength="6">
            </div>
            <div class="form-group">
                <label>Rol</label>
                <select name="role" id="edit-role" class="form-control">
                    <option value="consulta">Consulta</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="flex gap-1">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('modal-edit').classList.remove('active')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function editUser(id, name, email, role) {
    document.getElementById('form-edit').action = '/users/' + id;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-email').value = email;
    document.getElementById('edit-role').value = role;
    document.getElementById('modal-edit').classList.add('active');
}
</script>
@endsection
