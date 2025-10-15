<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use Carbon\Carbon;

class ManagerController extends Controller
{
    /**
     * Get visits for the manager's department
     */
    public function getVisits(Request $request)
    {
        $user = $request->user();
        
        // Managers can only see visits for their department
        $query = Visit::with(['department', 'site', 'updatedBy'])
            ->where('department_id', $user->department_id)
            ->orderBy('scheduled_at', 'desc');
        
        // Apply filters if provided
        if ($request->has('date_from')) {
            $query->whereDate('scheduled_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('scheduled_at', '<=', $request->date_to);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $visits = $query->paginate(20);
        
        return response()->json($visits);
    }

    /**
     * Reschedule a visit
     */
    public function rescheduleVisit(Request $request, $visitId)
    {
        $request->validate([
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string'
        ]);
        
        $visit = Visit::findOrFail($visitId);
        $user = $request->user();
        
        // Verify access - manager can only modify visits in their department
        if ($visit->department_id !== $user->department_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para modificar esta visita'
            ], 403);
        }
        
        // Can't reschedule completed or invalid visits
        if (in_array($visit->status, ['completed', 'cancelled']) || $visit->is_invalid) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede reagendar una visita completada, cancelada o inválida'
            ], 400);
        }
        
        $oldScheduled = $visit->scheduled_at;
        
        $visit->update([
            'scheduled_at' => $request->scheduled_at,
            'status' => 'scheduled',
            'updated_by' => $user->id,
            'notes' => $request->notes ? ($visit->notes ? $visit->notes . "\n[REAGENDADA] " . $request->notes : "[REAGENDADA] " . $request->notes) : $visit->notes
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Visita reagendada exitosamente',
            'visit' => $visit->load(['department', 'site', 'updatedBy']),
            'old_scheduled' => $oldScheduled
        ]);
    }

    /**
     * Confirm a visit
     */
    public function confirmVisit(Request $request, $visitId)
    {
        $request->validate([
            'notes' => 'nullable|string'
        ]);
        
        $visit = Visit::findOrFail($visitId);
        $user = $request->user();
        
        // Verify access
        if ($visit->department_id !== $user->department_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para modificar esta visita'
            ], 403);
        }
        
        // Can only confirm scheduled visits
        if ($visit->status !== 'scheduled') {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden confirmar visitas programadas'
            ], 400);
        }
        
        $visit->update([
            'status' => 'confirmed',
            'updated_by' => $user->id,
            'notes' => $request->notes ? ($visit->notes ? $visit->notes . "\n[CONFIRMADA] " . $request->notes : "[CONFIRMADA] " . $request->notes) : $visit->notes
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Visita confirmada exitosamente',
            'visit' => $visit->load(['department', 'site', 'updatedBy'])
        ]);
    }

    /**
     * Cancel a visit
     */
    public function cancelVisit(Request $request, $visitId)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);
        
        $visit = Visit::findOrFail($visitId);
        $user = $request->user();
        
        // Verify access
        if ($visit->department_id !== $user->department_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para modificar esta visita'
            ], 403);
        }
        
        // Can't cancel completed visits or visits that are already checked in
        if (in_array($visit->status, ['completed', 'arrived'])) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede cancelar una visita completada o que ya está en curso'
            ], 400);
        }
        
        $cancelNote = "[CANCELADA - " . $request->reason . "]";
        if ($request->notes) {
            $cancelNote .= " " . $request->notes;
        }
        
        $visit->update([
            'status' => 'cancelled',
            'updated_by' => $user->id,
            'notes' => $visit->notes ? $visit->notes . "\n" . $cancelNote : $cancelNote
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Visita cancelada exitosamente',
            'visit' => $visit->load(['department', 'site', 'updatedBy'])
        ]);
    }

    /**
     * Get statistics for manager's department
     */
    public function getStatistics(Request $request)
    {
        $user = $request->user();
        
        $query = Visit::where('department_id', $user->department_id);
        
        $today = Carbon::today();
        
        $stats = [
            'total_visits' => (clone $query)->count(),
            'visits_today' => (clone $query)->whereDate('scheduled_at', $today)->count(),
            'scheduled_visits' => (clone $query)->where('status', 'scheduled')->count(),
            'confirmed_visits' => (clone $query)->where('status', 'confirmed')->count(),
            'completed_visits' => (clone $query)->where('status', 'completed')->count(),
            'cancelled_visits' => (clone $query)->where('status', 'cancelled')->count(),
            'pending_approval' => (clone $query)->where('status', 'scheduled')->whereDate('scheduled_at', '>=', $today)->count()
        ];
        
        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }
}
