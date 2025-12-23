@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/usuario.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <h2>Registrar Préstamo</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('prestamos.store') }}" method="POST" novalidate>
        @csrf

        <div class="mb-3">
            <label for="codigo">Código <span class="text-danger">*</span></label>
            <input type="text" name="codigo" id="codigo" class="form-control"
                   value="{{ old('codigo') }}" required maxlength="50"
                   placeholder="Ej: PREST-001">
            <small class="form-text text-muted">Identificador único del préstamo.</small>
        </div>

        <div class="mb-3">
            <label for="usuario_id">Usuario <span class="text-danger">*</span></label>
            <select name="usuario_id" id="usuario_id" class="form-control" required>
                <option value="">Seleccione un usuario</option>
                @foreach($usuarios as $usuario)
                <option value="{{ $usuario->id }}" {{ old('usuario_id') == $usuario->id ? 'selected' : '' }}>
                    {{ $usuario->nombre }} {{ $usuario->apellido }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="recurso_id">Recurso <span class="text-danger">*</span></label>
            <select name="recurso_id" id="recurso_id" class="form-control" required>
                <option value="">Seleccione un recurso</option>
                @foreach($recursos as $recurso)
                <option value="{{ $recurso->id }}" {{ old('recurso_id') == $recurso->id ? 'selected' : '' }}>
                    {{ $recurso->nombre }}
                </option>
                @endforeach
            </select>
            <div id="recurso-error" class="text-danger small mt-1" style="display: none;"></div>
        </div>

        <div class="mb-3">
            <label for="fecha_prestamo">Fecha Préstamo <span class="text-danger">*</span></label>
            <input type="date" name="fecha_prestamo" id="fecha_prestamo" class="form-control"
                   value="{{ old('fecha_prestamo') }}" required>
        </div>

        <div class="mb-3">
            <label for="fecha_devolucion">Fecha Devolución</label>
            <input type="date" name="fecha_devolucion" id="fecha_devolucion" class="form-control"
                   value="{{ old('fecha_devolucion') }}">
        </div>

        <div class="mb-3">
            <label for="estado">Estado <span class="text-danger">*</span></label>
            <select name="estado" id="estado" class="form-control" required>
                <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="devuelto" {{ old('estado') == 'devuelto' ? 'selected' : '' }}>Devuelto</option>
                <option value="no devuelto" {{ old('estado') == 'no devuelto' ? 'selected' : '' }}>No devuelto</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success" id="guardar-btn">Guardar</button>
        <a href="{{ route('prestamos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const recursoSelect = document.getElementById('recurso_id');
    const errorDiv = document.getElementById('recurso-error');
    const guardarBtn = document.getElementById('guardar-btn');
    let timer = null;

    recursoSelect.addEventListener('change', function () {
        const recursoId = this.value;
        errorDiv.style.display = 'none';
        guardarBtn.disabled = false;

        if (!recursoId) return;

        clearTimeout(timer);
        timer = setTimeout(() => {
            fetch("/recursos/validar-disponibilidad", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ recurso_id: recursoId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.activo) {
                    errorDiv.innerText = 'Este recurso ya está en préstamo activo.';
                    errorDiv.style.display = 'block';
                    guardarBtn.disabled = true;
                } else {
                    errorDiv.innerText = '';
                    errorDiv.style.display = 'none';
                    guardarBtn.disabled = false;
                }
            });
        }, 400);
    });
});
</script>
@endsection