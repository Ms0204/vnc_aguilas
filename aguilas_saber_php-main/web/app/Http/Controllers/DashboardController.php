<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Prestamo;
use App\Models\Recurso;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        // 🔹 Totales simples
        $totalProductos     = Producto::count();
        $totalDevoluciones  = Prestamo::whereRaw("LOWER(estado) = 'devuelto'")->count();
        $totalNoDevueltos   = Prestamo::whereRaw("LOWER(estado) = 'no devuelto'")->count();

        // 🔹 Productos por mes (PostgreSQL compatible)
        $productosMes = Producto::whereNotNull('fecha_entrada')
            ->selectRaw('EXTRACT(MONTH FROM fecha_entrada) AS mes, COUNT(*) AS total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $mesLabels = [];
        $mesData   = [];

        foreach ($productosMes as $registro) {
            $mesLabels[] = Carbon::create()->month((int) $registro->mes)->translatedFormat('F'); // Ej: "Enero"
            $mesData[]   = $registro->total;
        }

        // 🔹 Estados de recursos agrupados desde PHP para mayor flexibilidad
        $estadoRecursosRaw = Recurso::all()
            ->groupBy(function ($recurso) {
                return ucfirst(strtolower($recurso->estado));
            });

        $estadoRecursos = collect([]);
        foreach ($estadoRecursosRaw as $estado => $items) {
            $estadoRecursos->put($estado, count($items));
        }

        // 🔹 Convertir todo a arrays planos antes de pasarlo a la vista
        $mesLabels      = collect($mesLabels)->toArray();
        $mesData        = collect($mesData)->toArray();
        $estadoRecursos = $estadoRecursos->toArray();

        return view('dashboard', compact(
            'totalProductos',
            'totalDevoluciones',
            'totalNoDevueltos',
            'mesLabels',
            'mesData',
            'estadoRecursos'
        ));
    }
}