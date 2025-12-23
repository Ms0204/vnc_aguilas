@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/usuario.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <h2>Recursos</h2>

    <a href="{{ route('recursos.create') }}" class="btn btn-primary mb-3">Agregar Recurso</a>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- 🔍 Buscador sin ícono --}}
    <div class="mb-3">
        <input type="text" id="search-recursos" class="form-control" placeholder="Buscar recurso por nombre, estado o descripción..." autocomplete="off">
    </div>

    {{-- 📋 Tabla de recursos --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Cantidad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="recursos-body">
            @forelse ($recursos as $recurso)
            <tr>
                <td data-label="Nombre">{{ $recurso->nombre }}</td>
                <td data-label="Descripción">{{ $recurso->descripcion }}</td>
                <td data-label="Estado">
                    @if ($recurso->estado === 'Disponible')
                        <span class="badge badge-disponible">Disponible</span>
                    @else
                        <span class="badge badge-no-disponible">{{ $recurso->estado }}</span>
                    @endif
                </td>
                <td data-label="Cantidad">{{ $recurso->cantidad }}</td>
                <td data-label="Acciones">
                    <a href="{{ route('recursos.edit', $recurso) }}" class="btn btn-warning btn-sm w-auto">✏️ Editar</a>
                    <form action="{{ route('recursos.destroy', $recurso) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-auto">🗑️ Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted">No se han encontrado resultados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 📄 Paginación --}}
    <div class="d-flex justify-content-center" id="recursos-paginacion">
        {{ $recursos->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('search-recursos');
    const tableBody = document.getElementById('recursos-body');
    const paginacion = document.getElementById('recursos-paginacion');
    let timer = null;

    input.addEventListener('input', function () {
        const query = this.value.trim();
        clearTimeout(timer);

        paginacion.style.display = query ? 'none' : 'block';

        timer = setTimeout(() => {
            fetch(`/recursos/buscar?search=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    tableBody.innerHTML = '';

                    if (data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No se encontraron recursos.</td></tr>';
                        return;
                    }

                    data.forEach(recurso => {
                        const badge = recurso.estado === 'Disponible'
                            ? '<span class="badge badge-disponible">Disponible</span>'
                            : `<span class="badge badge-no-disponible">${recurso.estado}</span>`;

                        tableBody.innerHTML += `
                            <tr>
                                <td data-label="Nombre">${recurso.nombre}</td>
                                <td data-label="Descripción">${recurso.descripcion ?? ''}</td>
                                <td data-label="Estado">${badge}</td>
                                <td data-label="Cantidad">${recurso.cantidad}</td>
                                <td data-label="Acciones">
                                    <a href="/recursos/${recurso.id}/edit" class="btn btn-warning btn-sm w-auto">✏️ Editar</a>
                                    <form method="POST" action="/recursos/${recurso.id}" style="display:inline-block;">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-danger btn-sm w-auto">🗑️ Eliminar</button>
                                    </form>
                                </td>
                            </tr>`;
                    });
                });
        }, 500);
    });
});
</script>
@endsection