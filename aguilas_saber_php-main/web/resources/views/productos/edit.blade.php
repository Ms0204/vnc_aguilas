@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/usuario.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <h2>Editar Producto</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('productos.update', $producto) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" id="nombre" class="form-control"
                   value="{{ old('nombre', $producto->nombre) }}" required maxlength="100"
                   placeholder="Ej: Monitor LED 24 pulgadas">
            <small class="form-text text-muted">El nombre debe ser único.</small>
            <div id="nombre-error" class="text-danger small mt-1" style="display: none;"></div>
        </div>

        <div class="mb-3">
            <label for="estado">Estado <span class="text-danger">*</span></label>
            <select name="estado" id="estado" class="form-control" required>
                <option value="disponible" {{ $producto->estado == 'disponible' ? 'selected' : '' }}>Disponible</option>
                <option value="agotado" {{ $producto->estado == 'agotado' ? 'selected' : '' }}>Agotado</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha_entrada">Fecha Entrada <span class="text-danger">*</span></label>
            <input type="date" name="fecha_entrada" id="fecha_entrada" class="form-control"
                   value="{{ old('fecha_entrada', $producto->fecha_entrada) }}" required>
        </div>

        <div class="mb-3">
            <label for="fecha_salida">Fecha Salida <span class="text-danger">*</span></label>
            <input type="date" name="fecha_salida" id="fecha_salida" class="form-control"
                   value="{{ old('fecha_salida', $producto->fecha_salida) }}" required>
        </div>

        <div class="mb-3">
            <label for="cantidad">Cantidad <span class="text-danger">*</span></label>
            <input type="number" name="cantidad" id="cantidad" class="form-control"
                   value="{{ old('cantidad', $producto->cantidad) }}" min="0" required placeholder="Ej: 5">
        </div>

        <button type="submit" class="btn btn-primary" id="actualizar-btn">Actualizar</button>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nombreInput = document.getElementById('nombre');
    const errorDiv = document.getElementById('nombre-error');
    const actualizarBtn = document.getElementById('actualizar-btn');
    const nombreOriginal = "{{ $producto->nombre }}".toLowerCase();
    let timer = null;

    nombreInput.addEventListener('input', function () {
        const nombre = nombreInput.value.trim().toLowerCase();
        clearTimeout(timer);

        if (!nombre || nombre === nombreOriginal) {
            errorDiv.style.display = 'none';
            actualizarBtn.disabled = false;
            return;
        }

        timer = setTimeout(() => {
            fetch("{{ route('productos.validarNombre') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ nombre })
            })
            .then(res => res.json())
            .then(data => {
                if (data.existe) {
                    errorDiv.innerText = '⚠️ Este producto ya existe';
                    errorDiv.style.display = 'block';
                    actualizarBtn.disabled = true;
                } else {
                    errorDiv.innerText = '';
                    errorDiv.style.display = 'none';
                    actualizarBtn.disabled = false;
                }
            });
        }, 500);
    });
});
</script>
@endsection