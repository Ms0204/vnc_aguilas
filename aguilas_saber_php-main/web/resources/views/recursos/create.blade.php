@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/usuario.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <h2>Agregar Recurso</h2>

    @if ($errors->has('nombre'))
        <div class="alert alert-warning">{{ $errors->first('nombre') }}</div>
    @endif

    <form action="{{ route('recursos.store') }}" method="POST" novalidate>
        @csrf

        <div class="mb-3">
            <label for="nombre">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" id="nombre" class="form-control"
                value="{{ old('nombre') }}" required maxlength="100"
                placeholder="Ej: Extintor, Laptop HP, Proyector Epson" autofocus>
            <small class="form-text text-muted">El nombre debe ser único y descriptivo.</small>
            <div id="nombre-error" class="text-danger small mt-1" style="display: none;"></div>
            @error('nombre')
                <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="descripcion">Descripción</label>
            <input type="text" name="descripcion" id="descripcion" class="form-control"
                value="{{ old('descripcion') }}" maxlength="255"
                placeholder="Opcional: detalles relevantes del recurso">
        </div>

        <div class="mb-3">
            <label for="cantidad">Cantidad <span class="text-danger">*</span></label>
            <input type="number" name="cantidad" id="cantidad" class="form-control"
                value="{{ old('cantidad', 1) }}" required min="1"
                placeholder="Ej: 5">
            <small class="form-text text-muted">Cantidad debe ser al menos 1 unidad.</small>
        </div>

        <div class="mb-3">
            <label for="estado">Estado <span class="text-danger">*</span></label>
            <select name="estado" id="estado" class="form-control" required>
                <option value="" disabled {{ old('estado') ? '' : 'selected' }}>-- Selecciona el estado --</option>
                <option value="bueno" {{ old('estado') == 'bueno' ? 'selected' : '' }}>Bueno</option>
                <option value="regular" {{ old('estado') == 'regular' ? 'selected' : '' }}>Regular</option>
                <option value="deteriorado" {{ old('estado') == 'deteriorado' ? 'selected' : '' }}>Deteriorado</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success" id="guardar-btn">Guardar</button>
        <a href="{{ route('recursos.index') }}" class="btn btn-secondary">Cancelar</a>
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

    function verificarNombre(nombre) {
        fetch("{{ route('recursos.validarNombre') }}", {
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
                errorDiv.innerText = 'Este recurso ya existe';
                errorDiv.style.display = 'block';
                guardarBtn.disabled = true;
            } else {
                errorDiv.innerText = '';
                errorDiv.style.display = 'none';
                guardarBtn.disabled = false;
            }
        });
    }

    nombreInput.addEventListener('input', function () {
        const nombre = nombreInput.value.trim();

        // cancelar verificación anterior si está escribiendo rápido
        clearTimeout(timer);

        if (!nombre) {
            errorDiv.style.display = 'none';
            guardarBtn.disabled = false;
            return;
        }

        // esperar 500ms para no saturar la petición mientras escribe
        timer = setTimeout(() => verificarNombre(nombre), 500);
    });
});
</script>
@endsection