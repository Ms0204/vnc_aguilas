@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="home-header">
        <h1 class="fade-up">Bienvenido a Aguilas del Saber</h1>
        <p class="subtext">Una plataforma integral para gestionar usuarios, préstamos, productos y más de forma eficiente.</p>
    </div>

    <div class="row fade-in">
        @php
            $cards = [
                ['icon' => 'fas fa-users', 'color' => 'primary', 'title' => 'Gestión de Usuarios', 'text' => 'Administra información de usuarios registrados.', 'href' => route('usuarios.index')],
                ['icon' => 'fas fa-archive', 'color' => 'success', 'title' => 'Gestión de Préstamos', 'text' => 'Supervisa los préstamos realizados.', 'href' => route('prestamos.index')],
                ['icon' => 'fas fa-shopping-cart', 'color' => 'danger', 'title' => 'Gestión de Productos', 'text' => 'Organiza tu inventario disponible.', 'href' => route('productos.index')],
                ['icon' => 'fas fa-database', 'color' => 'info', 'title' => 'Recursos', 'text' => 'Controla y clasifica los recursos.', 'href' => route('recursos.index')],
                ['icon' => 'fas fa-user-shield', 'color' => 'dark', 'title' => 'Gestión de Roles', 'text' => 'Administra roles y permisos de usuarios.', 'href' => route('roles.index')],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card home-card h-100">
                    <div class="card-body text-center">
                        <i class="{{ $card['icon'] }} fa-3x text-{{ $card['color'] }} mb-3"></i>
                        <h5 class="card-title">{{ $card['title'] }}</h5>
                        <p class="card-text">{{ $card['text'] }}</p>
                        <a href="{{ $card['href'] }}" class="btn btn-{{ $card['color'] }}">Acceder</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection