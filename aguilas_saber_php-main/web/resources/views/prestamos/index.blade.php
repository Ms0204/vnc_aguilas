@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/usuario.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <h2>Préstamos</h2>

    <a href="{{ route('prestamos.create') }}" class="btn btn-primary mb-3">Agregar Préstamo</a>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- 🔍 Búsqueda en tiempo real --}}
    <div class="mb-3">
        <input type="text" id="search-prestamos" class="form-control" placeholder="Buscar por código, usuario, recurso...">
    </div>

    {{-- 📋 Tabla principal --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Código</th>
                <th>Usuario</th>
                <th>Recurso</th>
                <th>Fecha Préstamo</th>
                <th>Fecha Devolución</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="prestamos-body">
            @forelse($prestamos as $prestamo)
            <tr>
                <td data-label="Código">{{ $prestamo->codigo }}</td>
                <td data-label="Usuario">{{ $prestamo->usuario->nombre ?? '' }} {{ $prestamo->usuario->apellido ?? '' }}</td>
                <td data-label="Recurso">{{ $prestamo->recurso->nombre ?? '' }}</td>
                <td data-label="Fecha Préstamo">{{ $prestamo->fecha_prestamo }}</td>
                <td data-label="Fecha Devolución">{{ $prestamo->fecha_devolucion }}</td>
                <td data-label="Estado">
                    @if ($prestamo->estado === 'Activo')
                        <span class="badge badge-disponible">Activo</span>
                    @else
                        <span class="badge badge-no-disponible">{{ $prestamo->estado }}</span>
                    @endif
                </td>
                <td data-label="Acciones">
                    <a href="{{ route('prestamos.edit', $prestamo) }}" class="btn btn-warning btn-sm w-auto">✏️ Editar</a>
                    <form action="{{ route('prestamos.destroy', $prestamo) }}" method="POST" style="display:inline-block;">
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

    {{-- 📄 Paginación --}}
    <div class="d-flex justify-content-center" id="prestamos-paginacion">
        {{ $prestamos->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('search-prestamos');
    const tableBody = document.getElementById('prestamos-body');
    const paginacion = document.getElementById('prestamos-paginacion');
    let timer = null;

    input.addEventListener('input', function () {
        const query = this.value.trim();
        clearTimeout(timer);

        if (!query) {
            paginacion.style.display = 'block';
            return;
        }

        paginacion.style.display = 'none';

        timer = setTimeout(() => {
            fetch(`/prestamos/buscar?search=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    tableBody.innerHTML = '';

                    if (data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No se encontraron préstamos.</td></tr>';
                        return;
                    }

                    data.forEach(prestamo => {
                        const estadoBadge = prestamo.estado === 'Activo'
                            ? '<span class="badge badge-disponible">Activo</span>'
                            : `<span class="badge badge-no-disponible">${prestamo.estado}</span>`;

                        tableBody.innerHTML += `
                            <tr>
                                <td data-label="Código">${prestamo.codigo}</td>
                                <td data-label="Usuario">${prestamo.usuario}</td>
                                <td data-label="Recurso">${prestamo.recurso}</td>
                                <td data-label="Fecha Préstamo">${prestamo.fecha_prestamo}</td>
                                <td data-label="Fecha Devolución">${prestamo.fecha_devolucion}</td>
                                <td data-label="Estado">${estadoBadge}</td>
                                <td data-label="Acciones">
                                    <a href="/prestamos/${prestamo.id}/edit" class="btn btn-warning btn-sm w-auto">✏️ Editar</a>
                                    <form method="POST" action="/prestamos/${prestamo.id}" style="display:inline-block;">
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