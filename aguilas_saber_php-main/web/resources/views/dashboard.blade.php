@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
@endsection

@section('content')
<h1 class="text-center mb-4">Dashboard - Águilas del Saber</h1>

<div class="container">
    <div class="dashboard-row">
        <!-- Tarjeta 1 -->
        <div class="dashboard-card">
            <div class="card-header bg-primary text-white text-center">
                Gestión de Productos
            </div>
            <div class="card-body">
                <canvas id="gestionProductosChart"></canvas>
            </div>
        </div>

        <!-- Tarjeta 2 -->
        <div class="dashboard-card">
            <div class="card-header bg-success text-white text-center">
                Productos por Mes
            </div>
            <div class="card-body">
                <canvas id="productosPorMesChart"></canvas>
            </div>
        </div>

        <!-- Tarjeta 3 -->
        <div class="dashboard-card">
            <div class="card-header bg-info text-white text-center">
                Estado de Recursos
            </div>
            <div class="card-body">
                <canvas id="estadoRecursosChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Traducción de meses al español
    function traducirMes(mesIngles) {
        const mapaMeses = {
            "January": "Enero", "February": "Febrero", "March": "Marzo", "April": "Abril",
            "May": "Mayo", "June": "Junio", "July": "Julio", "August": "Agosto",
            "September": "Septiembre", "October": "Octubre", "November": "Noviembre", "December": "Diciembre"
        };
        return mapaMeses[mesIngles] || mesIngles;
    }

    // Datos desde el controlador
    const productosData        = {!! json_encode([$totalProductos, $totalDevoluciones, $totalNoDevueltos]) !!};
    const productosMesLabelsEn = {!! json_encode($mesLabels) !!};
    const productosMesLabels   = productosMesLabelsEn.map(traducirMes);
    const productosMesData     = {!! json_encode($mesData) !!};
    const recursoLabels        = {!! json_encode(array_keys($estadoRecursos)) !!};
    const recursoData          = {!! json_encode(array_values($estadoRecursos)) !!};

    // 📦 Gestión de productos (con No Devueltos)
    new Chart(document.getElementById('gestionProductosChart'), {
        type: 'bar',
        data: {
            labels: ['Productos', 'Devoluciones', 'No Devueltos'],
            datasets: [{
                label: 'Cantidad',
                data: productosData,
                backgroundColor: ['#ff6384', '#36a2eb', '#e74c3c']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } }
        }
    });

    // 📅 Productos por mes (en español)
    new Chart(document.getElementById('productosPorMesChart'), {
        type: 'bar',
        data: {
            labels: productosMesLabels,
            datasets: [{
                label: 'Cantidad',
                data: productosMesData,
                backgroundColor: '#4bc0c0'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } }
        }
    });

    // 🔍 Estado de recursos
    new Chart(document.getElementById('estadoRecursosChart'), {
        type: 'pie',
        data: {
            labels: recursoLabels,
            datasets: [{
                data: recursoData,
                backgroundColor: ['#2ecc71', '#f1c40f', '#e74c3c', '#9b59b6']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } }
        }
    });
</script>
@endsection