<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        // 👁 Incluye los roles en la respuesta
        $usuarios = Usuario::with('roles')->orderBy('id', 'desc')->get();
        return response()->json($usuarios);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string',
            'apellido'  => 'required|string',
            'email'     => 'required|email|unique:usuarios,email',
            'telefono'  => 'required|string',
            'activo'    => 'required|boolean',
            'password'  => 'required|string|min:6',
            'roles'     => 'required|array'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $usuario = Usuario::create([
            'nombre'   => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'email'    => $validated['email'],
            'telefono' => $validated['telefono'],
            'activo'   => $validated['activo'],
            'password' => $validated['password'],
        ]);

        $usuario->syncRoles($validated['roles']); // ✅ Asigna roles reales

        $usuario->load('roles'); // ✅ Incluye los roles en la respuesta

        return response()->json($usuario, 201);
    }

    public function show($id)
    {
        $usuario = Usuario::with('roles')->findOrFail($id);
        return response()->json($usuario);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $validated = $request->validate([
            'nombre'   => 'required|string',
            'apellido' => 'required|string',
            'email'    => 'required|email|unique:usuarios,email,' . $usuario->id,
            'telefono' => 'required|string',
            'activo'   => 'required|boolean',
            'password' => 'nullable|string|min:6',
            'roles'    => 'nullable|array'
        ]);

        $usuario->update([
            'nombre'   => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'email'    => $validated['email'],
            'telefono' => $validated['telefono'],
            'activo'   => $validated['activo'],
        ]);

        if (!empty($validated['password'])) {
            $usuario->password = Hash::make($validated['password']);
            $usuario->save();
        }

        if (!empty($validated['roles'])) {
            $usuario->syncRoles($validated['roles']);
        }

        $usuario->load('roles');

        return response()->json($usuario);
    }

    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->roles()->detach(); // ✅ Limpia los roles asignados
        $usuario->delete();

        return response()->json(null, 204);
    }
}