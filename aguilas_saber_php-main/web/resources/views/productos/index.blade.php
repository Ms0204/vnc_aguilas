@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/usuario.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <h2>Productos</h2>

    <a href="{{ route('productos.create') }}" class="btn btn-primary mb-3">Agregar Producto</a>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- 🔍 Buscador en tiempo real --}}
    <div class="mb-3">
        <input type="text" id="search-productos" class="form-control" placeholder="Buscar por nombre o estado..." autocomplete="off">
    </div>

    {{-- 📋 Tabla de productos --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Fecha Entrada</th>
                <th>Fecha Salida</th>
                <th>Cantidad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="productos-body">
            @forelse($productos as $producto)
            <tr>
                <td data-label="Nombre">{{ $producto->nombre }}</td>
                <td data-label="Estado">
                    @if (strtolower($producto->estado) === 'disponible')
                        <span class="badge badge-disponible">Disponible</span>
                    @else
                        <span class="badge badge-no-disponible">Agotado</span>
                    @endif
                </td>
                <td data-label="Fecha Entrada">{{ $producto->fecha_entrada }}</td>
                <td data-label="Fecha Salida">{{ $producto->fecha_salida }}</td>
                <td data-label="Cantidad">{{ $producto->cantidad }}</td>
                <td data-label="Acciones">
                    @if (strtolower($producto->estado) === 'disponible')
                        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-warning btn-sm w-auto">✏️ Editar</a>
                    @else
                        <button class="btn btn-secondary btn-sm w-auto" disabled>❌ No editable</button>
                    @endif
                    <form action="{{ route('productos.destroy', $producto) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-auto">🗑️ Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">No se han encontrado resultados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 📄 Paginación --}}
    <div class="d-flex justify-content-center" id="productos-paginacion">
        {{ $productos->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('search-productos');
    const tableBody = document.getElementById('productos-body');
    const paginacion = document.getElementById('productos-paginacion');
    let timer = null;

    input.addEventListener('input', function () {
        const query = this.value.trim();
        clearTimeout(timer);

        paginacion.style.display = query ? 'none' : 'block';

        timer = setTimeout(() => {
            fetch(`/productos/buscar?search=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    tableBody.innerHTML = '';

                    if (data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No se encontraron productos.</td></tr>';
                        return;
                    }

                    data.forEach(prod => {
                        const badge = prod.estado.toLowerCase() === 'disponible'
                            ? '<span class="badge badge-disponible">Disponible</span>'
                            : '<span class="badge badge-no-disponible">Agotado</span>';

                        const botonActivo = prod.estado.toLowerCase() === 'disponible'
                            ? `<a href="/productos/${prod.id}/edit" class="btn btn-warning btn-sm w-auto">✏️ Editar</a>`
                            : `<button class="btn btn-secondary btn-sm w-auto" disabled>❌ No editable</button>`;

                        tableBody.innerHTML += `
                            <tr>
                                <td data-label="Nombre">${prod.nombre}</td>
                                <td data-label="Estado">${badge}</td>
                                <td data-label="Fecha Entrada">${prod.fecha_entrada ?? ''}</td>
                                <td data-label="Fecha Salida">${prod.fecha_salida ?? ''}</td>
                                <td data-label="Cantidad">${prod.cantidad}</td>
                                <td data-label="Acciones">
                                    ${botonActivo}
                                    <form method="POST" action="/productos/${prod.id}" style="display:inline-block;">
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