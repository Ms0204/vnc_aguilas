<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recurso;
use Illuminate\Http\Request;

class RecursoController extends Controller
{
    public function index(Request $request)
    {
        $query = Recurso::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%$search%")
                  ->orWhere('estado', 'ILIKE', "%$search%");
            });
        }

        return response()->json($query->orderBy('id', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'descripcion' => 'required|string',
            'cantidad' => 'required|integer|min:0',
            'estado' => 'required|string'
        ]);

        $recurso = Recurso::create($validated);

        return response()->json($recurso, 201);
    }

    public function show($id)
    {
        $recurso = Recurso::findOrFail($id);
        return response()->json($recurso);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'descripcion' => 'required|string',
            'cantidad' => 'required|integer|min:0',
            'estado' => 'required|string'
        ]);

        $recurso = Recurso::findOrFail($id);
        $recurso->update($validated);

        return response()->json($recurso);
    }

    public function destroy($id)
    {
        $recurso = Recurso::findOrFail($id);
        $recurso->delete();

        return response()->json(null, 204);
    }
}