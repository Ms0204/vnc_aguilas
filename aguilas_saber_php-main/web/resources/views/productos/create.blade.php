@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/usuario.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <h2>Agregar Producto</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('productos.store') }}" method="POST" novalidate>
        @csrf

        <div class="mb-3">
            <label for="nombre">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" id="nombre" class="form-control"
                   value="{{ old('nombre') }}" placeholder="Ej: Teclado inalámbrico" required maxlength="100">
            <small class="form-text text-muted">Debe ser único y descriptivo.</small>
            <div id="nombre-error" class="text-danger small mt-1" style="display: none;"></div>
        </div>

        <div class="mb-3">
            <label for="estado">Estado <span class="text-danger">*</span></label>
            <select name="estado" id="estado" class="form-control" required>
                <option value="disponible" {{ old('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                <option value="agotado" {{ old('estado') == 'agotado' ? 'selected' : '' }}>Agotado</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha_entrada">Fecha Entrada <span class="text-danger">*</span></label>
            <input type="date" name="fecha_entrada" id="fecha_entrada" class="form-control"
                   value="{{ old('fecha_entrada') }}" required>
        </div>

        <div class="mb-3">
            <label for="fecha_salida">Fecha Salida <span class="text-danger">*</span></label>
            <input type="date" name="fecha_salida" id="fecha_salida" class="form-control"
                   value="{{ old('fecha_salida') }}" required>
        </div>

        <div class="mb-3">
            <label for="cantidad">Cantidad <span class="text-danger">*</span></label>
            <input type="number" name="cantidad" id="cantidad" class="form-control"
                   value="{{ old('cantidad', 0) }}" min="0" required placeholder="Ej: 10">
        </div>

        <button type="submit" class="btn btn-success" id="guardar-btn">Guardar</button>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nombreInput = document.getElementById('nombre');
    const errorDiv = document.getElementById('nombre-error');
    const guardarBtn = document.getElementById('guardar-btn');
    let timer = null;

    nombreInput.addEventListener('input', function () {
        const nombre = nombreInput.value.trim();
        clearTimeout(timer);

        if (!nombre) {
            errorDiv.style.display = 'none';
            guardarBtn.disabled = false;
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
                    guardarBtn.disabled = true;
                } else {
                    errorDiv.innerText = '';
                    errorDiv.style.display = 'none';
                    guardarBtn.disabled = false;
                }
            });
        }, 500);
    });
});
</script>
@endsection