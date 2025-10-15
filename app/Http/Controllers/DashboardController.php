<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Visit;
use App\Models\Site;
use App\Models\Department;

class DashboardController extends Controller
{
    // Constructor removed - using route middleware instead

    public function getVisits(Request $request)
    {
        $user = $request->user();
        $query = Visit::with(['site', 'department']);

        // Aplicar filtros según el rol del usuario
        switch ($user->role) {
            case 'admin_master':
                // Puede ver todas las visitas
                break;
                
            case 'admin_site':
                // Solo visitas de su sitio
                $query->where('site_id', $user->site_id);
                break;
                
            case 'security':
                // Puede ver todas las visitas
                break;
                
            case 'manager':
                // Solo visitas de su departamento
                $query->where('department_id', $user->department_id);
                break;
        }

        // Aplicar filtros adicionales si se proporcionan
        if ($request->has('site_id') && $user->canViewSite($request->site_id)) {
            $query->where('site_id', $request->site_id);
        }

        if ($request->has('department_id') && $user->canViewDepartment($request->department_id)) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('scheduled_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('scheduled_at', '<=', $request->date_to);
        }

        $visits = $query->orderBy('scheduled_at', 'desc')->paginate(20);

        return response()->json($visits);
    }

    public function updateVisitStatus(Request $request, $id)
    {
        $user = $request->user();
        $visit = Visit::findOrFail($id);

        // Verificar permisos
        if (!$this->canManageVisit($user, $visit)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para modificar esta visita'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled'
        ]);

        $visit->update([
            'status' => $request->status,
            'updated_by' => $user->id
        ]);

        return response()->json([
            'success' => true,
            'visit' => $visit->load(['site', 'department']),
            'message' => 'Estado actualizado correctamente'
        ]);
    }

    public function getStats(Request $request)
    {
        $user = $request->user();
        $query = Visit::query();

        // Aplicar filtros según el rol del usuario
        switch ($user->role) {
            case 'admin_master':
                break;
            case 'admin_site':
                $query->where('site_id', $user->site_id);
                break;
            case 'security':
                break;
            case 'manager':
                $query->where('department_id', $user->department_id);
                break;
        }

        $stats = [
            'total_visits' => $query->count(),
            'pending_visits' => (clone $query)->where('status', 'pending')->count(),
            'confirmed_visits' => (clone $query)->where('status', 'confirmed')->count(),
            'checked_in_visits' => (clone $query)->where('status', 'checked_in')->count(),
            'todays_visits' => (clone $query)->whereDate('scheduled_at', today())->count(),
        ];

        return response()->json($stats);
    }

    private function canManageVisit($user, $visit)
    {
        switch ($user->role) {
            case 'admin_master':
                return true;
            case 'admin_site':
                return $visit->site_id == $user->site_id;
            case 'security':
                return true;
            case 'manager':
                return $visit->department_id == $user->department_id;
            default:
                return false;
        }
    }
}
