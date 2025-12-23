@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/usuario.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <h2>Agregar Rol</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('roles.store') }}" method="POST" novalidate>
        @csrf

        {{-- ✅ Campo 'name' en lugar de 'nombre' --}}
        <div class="mb-3">
            <label for="name">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control"
                   value="{{ old('name') }}" required maxlength="100"
                   placeholder="Ej: admin, usuario, editor"
                   title="Este nombre debe coincidir con el esperado por el sistema de roles">
        </div>

        <div class="mb-3">
            <label for="descripcion">Descripción</label>
            <input type="text" name="descripcion" id="descripcion" class="form-control"
                   value="{{ old('descripcion') }}"
                   placeholder="Ej: Permite gestionar usuarios, contenido, etc.">
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection