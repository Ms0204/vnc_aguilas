<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%$search%")
                    ->orWhere('estado', 'ILIKE', "%$search%");
            });
        }

        $productos = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('productos.index', compact('productos'));
    }

    public function buscar(Request $request)
    {
        $search = $request->input('search');

        $productos = Producto::where(function ($q) use ($search) {
            $q->where('nombre', 'ILIKE', "%{$search}%")
                ->orWhere('estado', 'ILIKE', "%{$search}%");
        })
            ->orderBy('id', 'desc')
            ->get(['id', 'nombre', 'estado', 'fecha_entrada', 'fecha_salida', 'cantidad']);

        return response()->json($productos);
    }


    public function create()
    {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'         => 'required|string|max:100',
            'estado'         => 'required|in:disponible,agotado',
            'fecha_entrada'  => 'required|date',
            'fecha_salida'   => 'required|date|after_or_equal:fecha_entrada',
            'cantidad'       => 'required|integer|min:0',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (Producto::where('nombre', $request->nombre)->exists()) {
                $validator->errors()->add('nombre', 'El producto ya existe');
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Producto::create($request->all());

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente');
    }

    public function show($id)
    {
        $producto = Producto::findOrFail($id);
        return view('productos.show', compact('producto'));
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre'         => 'required|string|max:100',
            'estado'         => 'required|in:disponible,agotado',
            'fecha_entrada'  => 'required|date',
            'fecha_salida'   => 'required|date|after_or_equal:fecha_entrada',
            'cantidad'       => 'required|integer|min:0',
        ]);

        $validator->after(function ($validator) use ($request, $id) {
            if (Producto::where('nombre', $request->nombre)
                ->where('id', '!=', $id)
                ->exists()
            ) {
                $validator->errors()->add('nombre', 'El producto ya existe');
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $producto = Producto::findOrFail($id);
        $producto->update($request->all());

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente');
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente');
    }

    // ✅ Verificación AJAX del nombre
    public function validarNombre(Request $request)
    {
        $nombre = $request->input('nombre');
        $existe = Producto::where('nombre', $nombre)->exists();

        return response()->json(['existe' => $existe]);
    }
}
