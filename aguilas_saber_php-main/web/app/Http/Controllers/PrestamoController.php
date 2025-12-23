<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use App\Models\Usuario;
use App\Models\Recurso;
use Illuminate\Http\Request;

class PrestamoController extends Controller
{
    public function index(Request $request)
    {
        $query = Prestamo::with(['usuario', 'recurso']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'ILIKE', "%$search%")
                    ->orWhereHas('usuario', function ($qu) use ($search) {
                        $qu->where('nombre', 'ILIKE', "%$search%")
                           ->orWhere('apellido', 'ILIKE', "%$search%");
                    })
                    ->orWhereHas('recurso', function ($qr) use ($search) {
                        $qr->where('nombre', 'ILIKE', "%$search%");
                    })
                    ->orWhere('estado', 'ILIKE', "%$search%");
            });
        }

        $prestamos = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        return view('prestamos.index', compact('prestamos'));
    }

    public function create()
    {
        $usuarios = Usuario::all();
        $recursos = Recurso::all();
        return view('prestamos.create', compact('usuarios', 'recursos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo'            => 'required|unique:prestamos,codigo',
            'usuario_id'        => 'required|exists:usuarios,id',
            'recurso_id'        => 'required|exists:recursos,id',
            'fecha_prestamo'    => 'required|date',
            'fecha_devolucion'  => 'nullable|date|after_or_equal:fecha_prestamo',
            'estado'            => 'required|in:pendiente,devuelto,no devuelto',
        ]);

        Prestamo::create($request->all());
        return redirect()->route('prestamos.index')->with('success', 'Préstamo creado correctamente');
    }

    public function show($id)
    {
        $prestamo = Prestamo::with(['usuario', 'recurso'])->findOrFail($id);
        return view('prestamos.show', compact('prestamo'));
    }

    public function edit($id)
    {
        $prestamo = Prestamo::findOrFail($id);
        $usuarios = Usuario::all();
        $recursos = Recurso::all();
        return view('prestamos.edit', compact('prestamo', 'usuarios', 'recursos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'codigo'            => 'required|unique:prestamos,codigo,' . $id,
            'usuario_id'        => 'required|exists:usuarios,id',
            'recurso_id'        => 'required|exists:recursos,id',
            'fecha_prestamo'    => 'required|date',
            'fecha_devolucion'  => 'nullable|date|after_or_equal:fecha_prestamo',
            'estado'            => 'required|in:pendiente,devuelto,no devuelto',
        ]);

        $prestamo = Prestamo::findOrFail($id);
        $prestamo->update($request->all());

        return redirect()->route('prestamos.index')->with('success', 'Préstamo actualizado correctamente');
    }

    // 🔍 Búsqueda en tiempo real
    public function buscar(Request $request)
    {
        $search = $request->input('search');

        $prestamos = Prestamo::with(['usuario', 'recurso'])
            ->where(function ($query) use ($search) {
                $query->where('codigo', 'ILIKE', "%{$search}%")
                    ->orWhereHas('usuario', function ($q) use ($search) {
                        $q->where('nombre', 'ILIKE', "%{$search}%")
                          ->orWhere('apellido', 'ILIKE', "%{$search}%");
                    })
                    ->orWhereHas('recurso', function ($q) use ($search) {
                        $q->where('nombre', 'ILIKE', "%{$search}%");
                    })
                    ->orWhere('estado', 'ILIKE', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->get();

        $data = $prestamos->map(function ($p) {
            return [
                'id'              => $p->id,
                'codigo'          => $p->codigo,
                'usuario'         => $p->usuario ? "{$p->usuario->nombre} {$p->usuario->apellido}" : '—',
                'recurso'         => $p->recurso ? $p->recurso->nombre : '—',
                'fecha_prestamo'  => $p->fecha_prestamo,
                'fecha_devolucion'=> $p->fecha_devolucion,
                'estado'          => $p->estado,
            ];
        });

        return response()->json($data);
    }

    public function destroy($id)
    {
        $prestamo = Prestamo::findOrFail($id);
        $prestamo->delete();

        return redirect()->route('prestamos.index')->with('success', 'Préstamo eliminado correctamente');
    }
}