@extends('layouts.guest')

@section('styles')
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="login-wrapper">
    <div class="login-box">
        <div class="logo-box text-center">
            <img src="{{ asset('static/img/fondo_aguilas_saber.png') }}" alt="Logo Aguilas del Saber" class="img-fluid mb-3" style="max-width: 100px;">
        </div>
        <h2 class="title">Iniciar Sesión</h2>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="input-group mb-3">
                <input type="email" name="email" class="form-control" placeholder="Correo electrónico" value="{{ old('email') }}" required autofocus>
                <span class="input-group-text"><i class="fas fa-user"></i></span>
            </div>
            <div class="mb-3 position-relative">
                <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required>
                <span id="toggle-password" class="toggle-icon"><i class="fas fa-eye-slash"></i></span>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="remember" id="remember_me" class="form-check-input">
                <label for="remember_me" class="form-check-label">Recordarme</label>
            </div>
            <button type="submit" class="btn btn-danger w-100">Iniciar sesión</button>
        </form>

        <div class="text-center mt-3">
            <a href="#" class="link-recover" data-bs-toggle="modal" data-bs-target="#recoverPasswordModal">¿Olvidaste tu contraseña?</a>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="recoverPasswordModal" tabindex="-1" aria-labelledby="recoverPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="recoverPasswordModalLabel">Recuperar Contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="recover_email" class="form-label">Correo electrónico</label>
                    <input type="email" name="email" id="recover_email" class="form-control mb-3" required>
                    <button type="submit" class="btn btn-danger w-100">Recuperar contraseña</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('toggle-password').addEventListener('click', function () {
        const input = document.getElementById('password');
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        this.innerHTML = isHidden ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });
</script>
@endsection