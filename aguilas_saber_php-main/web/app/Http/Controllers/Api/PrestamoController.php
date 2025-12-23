<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prestamo;
use Illuminate\Http\Request;

class PrestamoController extends Controller
{
    public function index()
    {
        $prestamos = Prestamo::with(['usuario', 'recurso'])->orderBy('id', 'desc')->get();
        return response()->json($prestamos);
    }

    public function store(Request $request)
    {
        $estado = strtolower($request->input('estado'));

        $rules = [
            'codigo'           => 'required|string|unique:prestamos,codigo',
            'usuario_id'       => 'required|exists:usuarios,id',
            'recurso_id'       => 'required|exists:recursos,id',
            'fecha_prestamo'   => 'required|date',
            'estado'           => 'required|in:pendiente,devuelto,no devuelto',
        ];

        // Solo validar fecha_devolucion si el estado es "devuelto"
        if ($estado === 'devuelto') {
            $rules['fecha_devolucion'] = 'required|date|after_or_equal:fecha_prestamo';
        }

        $validated = $request->validate($rules);

        // Si no fue devuelto, aseguramos que no haya fecha
        if ($estado !== 'devuelto') {
            $validated['fecha_devolucion'] = null;
        }

        $prestamo = Prestamo::create($validated);

        return response()->json([
            'message' => 'Préstamo registrado correctamente',
            'prestamo' => $prestamo
        ], 201);
    }

    public function show($id)
    {
        $prestamo = Prestamo::with(['usuario', 'recurso'])->findOrFail($id);
        return response()->json($prestamo);
    }

    public function update(Request $request, $id)
    {
        $estado = strtolower($request->input('estado'));

        $rules = [
            'codigo'           => 'required|string|unique:prestamos,codigo,' . $id,
            'usuario_id'       => 'required|exists:usuarios,id',
            'recurso_id'       => 'required|exists:recursos,id',
            'fecha_prestamo'   => 'required|date',
            'estado'           => 'required|in:pendiente,devuelto,no devuelto',
        ];

        if ($estado === 'devuelto') {
            $rules['fecha_devolucion'] = 'required|date|after_or_equal:fecha_prestamo';
        }

        $validated = $request->validate($rules);

        if ($estado !== 'devuelto') {
            $validated['fecha_devolucion'] = null;
        }

        $prestamo = Prestamo::findOrFail($id);
        $prestamo->update($validated);

        return response()->json([
            'message' => 'Préstamo actualizado correctamente',
            'prestamo' => $prestamo
        ]);
    }

    public function destroy($id)
    {
        $prestamo = Prestamo::findOrFail($id);
        $prestamo->delete();

        return response()->json([
            'message' => 'Préstamo eliminado correctamente'
        ]);
    }
}