<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Usuario;
use App\Models\Prestamo;
use App\Models\Recurso;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    // 📦 Reporte de productos
    public function productosPDF()
    {
        $productos = Producto::all();
        $pdf = Pdf::loadView('reportes.productos', compact('productos'));
        return $pdf->stream('reporte_productos.pdf');
    }

    public function descargarProductosPDF()
    {
        $productos = Producto::all();
        $pdf = Pdf::loadView('reportes.productos', compact('productos'));
        return $pdf->download('reporte_productos.pdf');
    }

    // 👥 Reporte de usuarios
    public function usuariosPDF()
    {
        $usuarios = Usuario::all();
        $pdf = Pdf::loadView('reportes.usuarios', compact('usuarios'));
        return $pdf->stream('reporte_usuarios.pdf');
    }

    public function descargarUsuariosPDF()
    {
        $usuarios = Usuario::all();
        $pdf = Pdf::loadView('reportes.usuarios', compact('usuarios'));
        return $pdf->download('reporte_usuarios.pdf');
    }

    // 📚 Reporte de préstamos
    public function prestamosPDF()
    {
        $prestamos = Prestamo::with('recurso', 'usuario')->get();
        $pdf = Pdf::loadView('reportes.prestamos', compact('prestamos'));
        return $pdf->stream('reporte_prestamos.pdf');
    }

    public function descargarPrestamosPDF()
    {
        $prestamos = Prestamo::with('recurso', 'usuario')->get();
        $pdf = Pdf::loadView('reportes.prestamos', compact('prestamos'));
        return $pdf->download('reporte_prestamos.pdf');
    }

    public function recursosPDF()
    {
        $recursos = Recurso::all();
        $pdf = Pdf::loadView('reportes.recursos', compact('recursos'));
        return $pdf->stream('reporte_recursos.pdf');
    }

    public function descargarRecursosPDF()
    {
        $recursos = Recurso::all();
        $pdf = Pdf::loadView('reportes.recursos', compact('recursos'));
        return $pdf->download('reporte_recursos.pdf');
    }
}
