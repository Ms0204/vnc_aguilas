<?php

namespace App\Http\Controllers;

use App\Models\Recurso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecursoController extends Controller
{
    public function index(Request $request)
    {
        $query = Recurso::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%$search%")
                    ->orWhere('descripcion', 'ILIKE', "%$search%");
            });
        }

        $recursos = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        return view('recursos.index', compact('recursos'));
    }

    public function create()
    {
        return view('recursos.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'cantidad'    => 'required|integer|min:0',
            'estado'      => 'required|in:bueno,regular,deteriorado',
        ]);

        // Validación personalizada para evitar duplicados antes de llegar al SQL
        $validator->after(function ($validator) use ($request) {
            if (Recurso::where('nombre', $request->nombre)->exists()) {
                $validator->errors()->add('nombre', 'El recurso ya existe');
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Todo válido, se guarda
        Recurso::create($request->only('nombre', 'descripcion', 'cantidad', 'estado'));
        return redirect()->route('recursos.index')->with('success', 'Recurso creado correctamente');
    }

    public function edit($id)
    {
        $recurso = Recurso::findOrFail($id);
        return view('recursos.edit', compact('recurso'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'cantidad'    => 'required|integer|min:0',
            'estado'      => 'required|in:bueno,regular,deteriorado',
        ]);

        // Validación personalizada excluyendo el ID actual
        $validator->after(function ($validator) use ($request, $id) {
            if (Recurso::where('nombre', $request->nombre)->where('id', '!=', $id)->exists()) {
                $validator->errors()->add('nombre', 'El recurso ya existe');
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $recurso = Recurso::findOrFail($id);
        $recurso->update($request->only('nombre', 'descripcion', 'cantidad', 'estado'));

        return redirect()->route('recursos.index')->with('success', 'Recurso actualizado correctamente');
    }

    public function verificarDisponibilidad(Request $request)
    {
        $id = $request->input('recurso_id');

        $activo = \App\Models\Prestamo::where('recurso_id', $id)
            ->where('estado', 'pendiente')
            ->exists();

        return response()->json(['activo' => $activo]);
    }


    public function buscar(Request $request)
    {
        $search = $request->input('search');

        $recursos = Recurso::query()
            ->where('nombre', 'ILIKE', "%{$search}%")
            ->orWhere('descripcion', 'ILIKE', "%{$search}%")
            ->orderBy('id', 'desc')
            ->get(['id', 'nombre', 'descripcion', 'estado', 'cantidad']);

        return response()->json($recursos);
    }


    public function destroy($id)
    {
        $recurso = Recurso::findOrFail($id);
        $recurso->delete();

        return redirect()->route('recursos.index')->with('success', 'Recurso eliminado correctamente');
    }

    // 🔎 AJAX: verificación en tiempo real desde el formulario
    public function validarNombre(Request $request)
    {
        $nombre = $request->input('nombre');
        $existe = Recurso::where('nombre', $nombre)->exists();

        return response()->json(['existe' => $existe]);
    }
}
