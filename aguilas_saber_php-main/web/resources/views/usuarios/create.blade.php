@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/usuario.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <h2>Agregar Usuario</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('usuarios.store') }}" method="POST" novalidate>
        @csrf

        <div class="mb-3">
            <label for="nombre">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" id="nombre" class="form-control"
                   value="{{ old('nombre') }}" required maxlength="50"
                   placeholder="Ej: Andrea, Juan"
                   title="Máximo 50 caracteres">
            <div id="nombre-error" class="text-danger small mt-1" style="display: none;"></div>
        </div>

        <div class="mb-3">
            <label for="apellido">Apellido <span class="text-danger">*</span></label>
            <input type="text" name="apellido" id="apellido" class="form-control"
                   value="{{ old('apellido') }}" required maxlength="50"
                   placeholder="Ej: García, López">
        </div>

        <div class="mb-3">
            <label for="email">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email" class="form-control"
                   value="{{ old('email') }}" required maxlength="100"
                   placeholder="ejemplo@correo.com"
                   title="Correo electrónico válido">
            <div id="email-error" class="text-danger small mt-1" style="display: none;"></div>
        </div>

        <div class="mb-3">
            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" id="telefono" class="form-control"
                   value="{{ old('telefono') }}" maxlength="20"
                   pattern="[0-9+\s\-()]*" title="Solo números y símbolos válidos"
                   placeholder="Ej: +593 987654321">
        </div>

        <div class="mb-3">
            <label for="password">Contraseña <span class="text-danger">*</span></label>
            <input type="password" name="password" id="password" class="form-control"
                   required minlength="8" maxlength="30" autocomplete="new-password"
                   placeholder="Mínimo 8 caracteres"
                   title="Mínimo 8 caracteres">
        </div>

        {{-- 🛡 Roles ajustado para Spatie --}}
        <div class="mb-3">
            <label for="roles">Roles <span class="text-danger">*</span></label>
            <select name="roles[]" id="roles" class="form-control" multiple required>
                @foreach ($roles as $rol)
                <option value="{{ $rol->id }}" {{ collect(old('roles'))->contains($rol->id) ? 'selected' : '' }}>
                    {{ $rol->name }} {{-- ✅ corregido: antes era $rol->nombre --}}
                </option>
                @endforeach
            </select>
            <small class="form-text text-muted">
                Usa <b>Ctrl</b> (o <b>Cmd</b> en Mac) para selección múltiple.
            </small>
        </div>

        <div class="form-check mb-3">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" class="form-check-input" id="activo" value="1"
                   {{ old('activo') ? 'checked' : '' }}>
            <label class="form-check-label" for="activo">Activo</label>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nombreInput = document.getElementById('nombre');
    const emailInput = document.getElementById('email');
    const submitBtn = document.querySelector('button[type="submit"]');
    let timer = null;

    function validarCampo(input, tipo) {
        const valor = input.value.trim();
        const errorDiv = document.getElementById(`${tipo}-error`);

        if (!valor) {
            errorDiv.style.display = 'none';
            submitBtn.disabled = false;
            return;
        }

        fetch(`/usuarios/validar-${tipo}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ [tipo]: valor })
        })
        .then(res => res.json())
        .then(data => {
            if (data.existe) {
                errorDiv.innerText = `El ${tipo} ya está registrado.`;
                errorDiv.style.display = 'block';
                submitBtn.disabled = true;
            } else {
                errorDiv.innerText = '';
                errorDiv.style.display = 'none';
                submitBtn.disabled = false;
            }
        });
    }

    nombreInput.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(() => validarCampo(nombreInput, 'nombre'), 400);
    });

    emailInput.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(() => validarCampo(emailInput, 'email'), 400);
    });
});
</script>
@endsection