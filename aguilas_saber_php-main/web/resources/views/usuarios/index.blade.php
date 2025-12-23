@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/usuario.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <h2>Usuarios</h2>

    <a href="{{ route('usuarios.create') }}" class="btn btn-primary mb-3">Agregar Usuario</a>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
        <input type="text" id="search-usuarios" class="form-control"
               placeholder="Buscar por nombre, apellido o correo..." autocomplete="off">
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Activo</th>
                <th>Roles</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="usuarios-body">
            @forelse ($usuarios as $usuario)
            <tr>
                <td data-label="Nombre">{{ $usuario->nombre }}</td>
                <td data-label="Apellido">{{ $usuario->apellido }}</td>
                <td data-label="Email">{{ $usuario->email }}</td>
                <td data-label="Teléfono">{{ $usuario->telefono }}</td>
                <td data-label="Activo">{{ $usuario->activo ? 'Sí' : 'No' }}</td>
                <td data-label="Roles">
                    @forelse ($usuario->roles as $rol)
                    <span class="badge bg-info">{{ $rol->name }}</span> {{-- 🔄 Corregido: 'nombre' → 'name' --}}
                    @empty
                    <span class="badge badge-no-disponible">Sin rol</span>
                    @endforelse
                </td>
                <td data-label="Acciones">
                    <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-warning btn-sm w-auto">✏️ Editar</a>
                    <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-auto">🗑️ Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted">No se han encontrado resultados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center" id="usuarios-paginacion">
        {{ $usuarios->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('search-usuarios');
    const tableBody = document.getElementById('usuarios-body');
    const paginacion = document.getElementById('usuarios-paginacion');
    let timer = null;

    input.addEventListener('input', function () {
        const query = this.value.trim();
        clearTimeout(timer);

        paginacion.style.display = query ? 'none' : 'block';

        timer = setTimeout(() => {
            fetch(`/usuarios/buscar?search=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    tableBody.innerHTML = '';

                    if (data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No se encontraron usuarios.</td></tr>';
                        return;
                    }

                    data.forEach(usuario => {
                        const rolesHTML = usuario.roles.length > 0
                            ? usuario.roles.map(r => `<span class="badge bg-info">${r.name}</span>`).join(' ') // 🔄 Corregido
                            : `<span class="badge badge-no-disponible">Sin rol</span>`;

                        tableBody.innerHTML += `
                            <tr>
                                <td data-label="Nombre">${usuario.nombre}</td>
                                <td data-label="Apellido">${usuario.apellido}</td>
                                <td data-label="Email">${usuario.email}</td>
                                <td data-label="Teléfono">${usuario.telefono}</td>
                                <td data-label="Activo">${usuario.activo ? 'Sí' : 'No'}</td>
                                <td data-label="Roles">${rolesHTML}</td>
                                <td data-label="Acciones">
                                    <a href="/usuarios/${usuario.id}/edit" class="btn btn-warning btn-sm w-auto">✏️ Editar</a>
                                    <form method="POST" action="/usuarios/${usuario.id}" style="display:inline-block;">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-danger btn-sm w-auto">🗑️ Eliminar</button>
                                    </form>
                                </td>
                            </tr>`;
                    });
                });
        }, 400);
    });
});
</script>
@endsection