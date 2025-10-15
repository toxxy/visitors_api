<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Site;
use Illuminate\Validation\Rule;

class DepartmentManagementController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Department::with('site');

        // Aplicar filtros según el rol del usuario
        if ($user->role === 'admin_site') {
            // Admin de sitio solo puede ver departamentos de su sitio
            $query->where('site_id', $user->site_id);
        }

        $departments = $query->orderBy('site_id')->orderBy('name')->get();

        return response()->json($departments);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $rules = [
            'name' => 'required|string|max:255',
            'site_id' => 'required|exists:sites,id',
            'active' => 'boolean',
        ];

        // Si es admin_site, restringir el sitio
        if ($user->role === 'admin_site') {
            $rules['site_id'] = ['required', Rule::in([$user->site_id])];
        }

        $request->validate($rules);

        // Verificar que no exista otro departamento con el mismo nombre en el mismo sitio
        $exists = Department::where('name', $request->name)
                           ->where('site_id', $request->site_id)
                           ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un departamento con ese nombre en el sitio seleccionado'
            ], 400);
        }

        $department = Department::create([
            'name' => $request->name,
            'site_id' => $request->site_id,
            'active' => $request->active ?? true,
        ]);

        $department->load('site');

        return response()->json([
            'success' => true,
            'department' => $department,
            'message' => 'Departamento creado exitosamente'
        ]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $department = Department::with('site')->findOrFail($id);

        // Verificar permisos
        if ($user->role === 'admin_site' && $department->site_id !== $user->site_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver este departamento'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'department' => $department
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $department = Department::findOrFail($id);

        // Verificar permisos
        if ($user->role === 'admin_site' && $department->site_id !== $user->site_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para modificar este departamento'
            ], 403);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'site_id' => 'required|exists:sites,id',
            'active' => 'boolean',
        ];

        // Si es admin_site, restringir el sitio
        if ($user->role === 'admin_site') {
            $rules['site_id'] = ['required', Rule::in([$user->site_id])];
        }

        $request->validate($rules);

        // Verificar que no exista otro departamento con el mismo nombre en el mismo sitio
        $exists = Department::where('name', $request->name)
                           ->where('site_id', $request->site_id)
                           ->where('id', '!=', $id)
                           ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un departamento con ese nombre en el sitio seleccionado'
            ], 400);
        }

        $department->update([
            'name' => $request->name,
            'site_id' => $request->site_id,
            'active' => $request->active ?? true,
        ]);

        $department->load('site');

        return response()->json([
            'success' => true,
            'department' => $department,
            'message' => 'Departamento actualizado exitosamente'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $department = Department::findOrFail($id);

        // Verificar permisos
        if ($user->role === 'admin_site' && $department->site_id !== $user->site_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar este departamento'
            ], 403);
        }

        // Verificar si tiene usuarios asignados
        if ($department->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el departamento porque tiene usuarios asignados'
            ], 400);
        }

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Departamento eliminado exitosamente'
        ]);
    }
}
