<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%$search%")
                    ->orWhere('apellido', 'ILIKE', "%$search%")
                    ->orWhere('email', 'ILIKE', "%$search%");
            });
        }

        $usuarios = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('usuarios.index', compact('usuarios'));
    }

    public function buscar(Request $request)
    {
        $search = $request->input('search');

        $usuarios = Usuario::with('roles')
            ->where(function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%{$search}%")
                    ->orWhere('apellido', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->get(['id', 'nombre', 'apellido', 'email', 'telefono', 'activo']);

        return response()->json($usuarios);
    }

    public function validarNombre(Request $request)
    {
        $existe = Usuario::where('nombre', $request->nombre)->exists();
        return response()->json(['existe' => $existe]);
    }

    public function validarEmail(Request $request)
    {
        $existe = Usuario::where('email', $request->email)->exists();
        return response()->json(['existe' => $existe]);
    }

    public function create()
    {
        $roles = Role::all();
        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'apellido' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:usuarios,email',
            'telefono' => 'nullable|string|max:20|regex:/^[0-9+\s\-()]*$/',
            'password' => 'required|string|min:8|max:30',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'activo' => 'nullable|in:0,1',
        ]);

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
            'activo' => $request->activo == '1',
        ]);

        $usuario->roles()->sync($request->roles);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente');
    }

    public function edit(Usuario $usuario)
    {
        $roles = Role::all();
        $usuario->load('roles');
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'apellido' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:usuarios,email,' . $usuario->id,
            'telefono' => 'nullable|string|max:20|regex:/^[0-9+\s\-()]*$/',
            'password' => 'nullable|string|min:8|max:30',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'activo' => 'nullable|in:0,1',
        ]);

        $usuario->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'activo' => $request->activo == '1',
        ]);

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
            $usuario->save();
        }

        $usuario->roles()->sync($request->roles);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(Usuario $usuario)
    {
        $usuario->roles()->detach();
        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente');
    }
}