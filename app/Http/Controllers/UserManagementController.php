<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Site;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = User::with(['site', 'department']);

        // Aplicar filtros según el rol del usuario
        if ($user->role === 'admin_site') {
            // Admin de sitio solo puede ver usuarios de su sitio
            $query->where(function($q) use ($user) {
                $q->where('site_id', $user->site_id)
                  ->orWhereNull('site_id'); // Usuarios sin sitio asignado
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin_master,admin_site,security,manager',
            'site_id' => 'nullable|exists:sites,id',
            'department_id' => 'nullable|exists:departments,id',
        ];

        // Si es admin_site, restringir el sitio
        if ($user->role === 'admin_site') {
            $rules['site_id'] = ['required', Rule::in([$user->site_id])];
            $rules['role'] = 'required|in:security,manager'; // No puede crear otros admins
        }

        $request->validate($rules);

        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'site_id' => $request->site_id,
            'department_id' => $request->department_id,
        ]);

        $newUser->load(['site', 'department']);

        return response()->json([
            'success' => true,
            'user' => $newUser,
            'message' => 'Usuario creado exitosamente'
        ]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $targetUser = User::with(['site', 'department'])->findOrFail($id);

        // Verificar permisos
        if ($user->role === 'admin_site' && $targetUser->site_id !== $user->site_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver este usuario'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'user' => $targetUser
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $targetUser = User::findOrFail($id);

        // Verificar permisos
        if ($user->role === 'admin_site' && $targetUser->site_id !== $user->site_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para modificar este usuario'
            ], 403);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin_master,admin_site,security,manager',
            'site_id' => 'nullable|exists:sites,id',
            'department_id' => 'nullable|exists:departments,id',
        ];

        // Si es admin_site, restringir opciones
        if ($user->role === 'admin_site') {
            $rules['site_id'] = ['required', Rule::in([$user->site_id])];
            $rules['role'] = 'required|in:security,manager';
        }

        $request->validate($rules);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'site_id' => $request->site_id,
            'department_id' => $request->department_id,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $targetUser->update($updateData);
        $targetUser->load(['site', 'department']);

        return response()->json([
            'success' => true,
            'user' => $targetUser,
            'message' => 'Usuario actualizado exitosamente'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $targetUser = User::findOrFail($id);

        // No se puede eliminar a sí mismo
        if ($user->id === $targetUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes eliminarte a ti mismo'
            ], 400);
        }

        // Verificar permisos
        if ($user->role === 'admin_site' && $targetUser->site_id !== $user->site_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar este usuario'
            ], 403);
        }

        $targetUser->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado exitosamente'
        ]);
    }
}
