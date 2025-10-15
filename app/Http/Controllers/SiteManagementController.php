<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Site;

class SiteManagementController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Solo admin_master puede ver sitios
        if ($user->role !== 'admin_master') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para acceder a esta función'
            ], 403);
        }

        $sites = Site::withCount(['departments', 'users'])->orderBy('name')->get();

        return response()->json($sites);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Solo admin_master puede crear sitios
        if ($user->role !== 'admin_master') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para crear sitios'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:sites,name',
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'active' => 'boolean',
        ]);

        $site = Site::create([
            'name' => $request->name,
            'location' => $request->location,
            'address' => $request->address,
            'active' => $request->active ?? true,
        ]);

        return response()->json([
            'success' => true,
            'site' => $site,
            'message' => 'Sitio creado exitosamente'
        ]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role !== 'admin_master') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver sitios'
            ], 403);
        }

        $site = Site::withCount(['departments', 'users'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'site' => $site
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role !== 'admin_master') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para modificar sitios'
            ], 403);
        }

        $site = Site::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:sites,name,' . $id,
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'active' => 'boolean',
        ]);

        $site->update([
            'name' => $request->name,
            'location' => $request->location,
            'address' => $request->address,
            'active' => $request->active ?? true,
        ]);

        return response()->json([
            'success' => true,
            'site' => $site,
            'message' => 'Sitio actualizado exitosamente'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role !== 'admin_master') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar sitios'
            ], 403);
        }

        $site = Site::findOrFail($id);

        // Verificar si tiene departamentos o usuarios asignados
        if ($site->departments()->count() > 0 || $site->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el sitio porque tiene departamentos o usuarios asignados'
            ], 400);
        }

        $site->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sitio eliminado exitosamente'
        ]);
    }
}
