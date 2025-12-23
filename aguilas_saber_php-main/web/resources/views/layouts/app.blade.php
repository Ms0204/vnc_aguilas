<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aguilas del Saber')</title>
    <link rel="icon" href="{{ asset('static/img/fondo_aguilas_saber.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    @yield('styles')
</head>

<body>
    <!-- 🔼 Barra hamburguesa solo visible en móviles -->
    <nav class="navbar navbar-dark bg-dark d-md-none">
        <div class="container-fluid justify-content-between">
            <span class="navbar-brand">Aguilas del Saber</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- 📱 Menú hamburguesa (offcanvas) -->
    <div class="offcanvas offcanvas-start text-bg-dark d-md-none" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Gestiones</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            @include('partials.sidebar')
        </div>
    </div>

    <div class="layout-wrapper">
        <!-- 🧭 Sidebar fijo para escritorio -->
        <nav id="sidebar" class="d-none d-md-flex flex-column">
            @include('partials.sidebar')
        </nav>

        <!-- 📄 Contenido principal -->
        <main id="main-content">
            @yield('content')
        </main>
    </div>

    <footer class="footer">
        © 2024 Aguilas del Saber. Todos los derechos reservados.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>