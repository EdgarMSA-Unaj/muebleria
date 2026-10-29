<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsuariosController extends Controller
{
    public function index()
    {
        $usuarios = User::with('roles')->latest()->get();
        $roles = Role::all();

        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:30',
            'role' => 'required|exists:roles,name'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'is_active' => true
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('usuarios.index')->with('success', "Usuario {$user->name} creado correctamente.");
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $usuario->id,
            'phone' => 'nullable|string|max:30',
            'role' => 'required|exists:roles,name',
            'password' => 'nullable|min:6',
            'is_active' => 'nullable|boolean'
        ]);

        $datos = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null
        ];

        if ($request->filled('password')) {
            $datos['password'] = Hash::make($validated['password']);
        }

        // Si no es el propio usuario en sesión, permite cambiar estado activo
        if ($usuario->id !== Auth::id()) {
            $datos['is_active'] = $request->boolean('is_active', true);
        }

        $usuario->update($datos);
        $usuario->syncRoles([$validated['role']]);

        return redirect()->route('usuarios.index')->with('success', "Usuario {$usuario->name} actualizado con éxito.");
    }

    public function toggleActivo(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return back()->withErrors(['error' => 'No puedes desactivar tu propia cuenta en sesión activa.']);
        }

        $nuevoEstado = !$usuario->is_active;
        $usuario->update(['is_active' => $nuevoEstado]);

        $msg = $nuevoEstado ? "Usuario {$usuario->name} activado." : "Usuario {$usuario->name} suspendido/desactivado.";
        return redirect()->route('usuarios.index')->with('success', $msg);
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return back()->withErrors(['error' => 'No puedes eliminar tu propia cuenta en sesión activa.']);
        }

        $nombre = $usuario->name;
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', "Usuario {$nombre} eliminado del sistema.");
    }
}
