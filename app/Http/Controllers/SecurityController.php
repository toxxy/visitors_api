<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SecurityController extends Controller
{
    /**
     * Get visits for the security user's site
     */
    public function getVisits(Request $request)
    {
        $user = $request->user();
        
        // Security users should have access to all sites
        // But we'll filter by site_id if they have one assigned
        $query = Visit::with(['department', 'site', 'updatedBy'])
            ->orderBy('scheduled_at', 'desc');
        
        if ($user->site_id) {
            $query->where('site_id', $user->site_id);
        }
        
        // Filter by date range if provided
        if ($request->has('date_from')) {
            $query->whereDate('scheduled_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('scheduled_at', '<=', $request->date_to);
        }
        
        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Search by ID if provided
        if ($request->has('search_id')) {
            $query->where('id', $request->search_id);
        }
        
        $visits = $query->paginate(20);
        
        return response()->json($visits);
    }
    
    /**
     * Check in a visitor
     */
    public function checkIn(Request $request, $visitId)
    {
        $request->validate([
            'notes' => 'nullable|string'
        ]);
        
        $visit = Visit::findOrFail($visitId);
        $user = $request->user();
        
        // Verify access
        if ($user->site_id && $visit->site_id !== $user->site_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para esta visita'
            ], 403);
        }
        
        // Check if visit is already invalid
        if ($visit->is_invalid) {
            return response()->json([
                'success' => false,
                'message' => 'Esta visita está marcada como inválida: ' . $visit->invalid_reason
            ], 400);
        }
        
        // Check if visitor has already checked out and is trying to check in again
        if ($visit->check_out_count > 0 && $visit->checked_out_at) {
            $visit->update([
                'is_invalid' => true,
                'invalid_reason' => 'Intento de check-in después de check-out',
                'updated_by' => $user->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Visita marcada como inválida: no se puede reutilizar después del check-out'
            ], 400);
        }
        
        // Perform check-in
        $visit->update([
            'status' => 'arrived',
            'checked_in_at' => now(),
            'check_in_count' => $visit->check_in_count + 1,
            'arrived_at' => $visit->arrived_at ?? now(),
            'updated_by' => $user->id,
            'notes' => $request->notes ? ($visit->notes ? $visit->notes . "\n" . $request->notes : $request->notes) : $visit->notes
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Check-in realizado exitosamente',
            'visit' => $visit->load(['department', 'site', 'updatedBy'])
        ]);
    }
    
    /**
     * Check out a visitor
     */
    public function checkOut(Request $request, $visitId)
    {
        $request->validate([
            'notes' => 'nullable|string'
        ]);
        
        $visit = Visit::findOrFail($visitId);
        $user = $request->user();
        
        // Verify access
        if ($user->site_id && $visit->site_id !== $user->site_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para esta visita'
            ], 403);
        }
        
        // Check if visit is invalid
        if ($visit->is_invalid) {
            return response()->json([
                'success' => false,
                'message' => 'Esta visita está marcada como inválida: ' . $visit->invalid_reason
            ], 400);
        }
        
        // Check if visitor hasn't checked in
        if ($visit->check_in_count === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede hacer check-out sin check-in previo'
            ], 400);
        }
        
        // Perform check-out
        $visit->update([
            'status' => 'completed',
            'checked_out_at' => now(),
            'check_out_count' => $visit->check_out_count + 1,
            'departed_at' => now(),
            'updated_by' => $user->id,
            'notes' => $request->notes ? ($visit->notes ? $visit->notes . "\n" . $request->notes : $request->notes) : $visit->notes
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Check-out realizado exitosamente',
            'visit' => $visit->load(['department', 'site', 'updatedBy'])
        ]);
    }
    
    /**
     * Reschedule a visit
     */
    public function reschedule(Request $request, $visitId)
    {
        $request->validate([
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string'
        ]);
        
        $visit = Visit::findOrFail($visitId);
        $user = $request->user();
        
        // Verify access
        if ($user->site_id && $visit->site_id !== $user->site_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para esta visita'
            ], 403);
        }
        
        // Can't reschedule completed or invalid visits
        if ($visit->status === 'completed' || $visit->is_invalid) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede reagendar una visita completada o inválida'
            ], 400);
        }
        
        $visit->update([
            'scheduled_at' => $request->scheduled_at,
            'status' => 'scheduled',
            'updated_by' => $user->id,
            'notes' => $request->notes ? ($visit->notes ? $visit->notes . "\n" . $request->notes : $request->notes) : $visit->notes
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Visita reagendada exitosamente',
            'visit' => $visit->load(['department', 'site', 'updatedBy'])
        ]);
    }
    
    /**
     * Get statistics for the security dashboard
     */
    public function getStatistics(Request $request)
    {
        $user = $request->user();
        
        $query = Visit::query();
        
        if ($user->site_id) {
            $query->where('site_id', $user->site_id);
        }
        
        $today = Carbon::today();
        
        $stats = [
            'total_visits_today' => (clone $query)->whereDate('scheduled_at', $today)->count(),
            'checked_in_today' => (clone $query)->whereDate('checked_in_at', $today)->count(),
            'currently_inside' => (clone $query)
                ->where('status', 'arrived')
                ->whereNotNull('checked_in_at')
                ->whereNull('checked_out_at')
                ->count(),
            'completed_today' => (clone $query)->whereDate('departed_at', $today)->count(),
            'invalid_visits' => (clone $query)->where('is_invalid', true)->count(),
            'pending_visits' => (clone $query)->where('status', 'scheduled')->whereDate('scheduled_at', '>=', $today)->count()
        ];
        
        // Get hourly breakdown for today
        $hourly_checkins = DB::table('visits')
            ->select(DB::raw('HOUR(checked_in_at) as hour, COUNT(*) as count'))
            ->whereDate('checked_in_at', $today)
            ->when($user->site_id, function($query) use ($user) {
                return $query->where('site_id', $user->site_id);
            })
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        
        $stats['hourly_checkins'] = $hourly_checkins;
        
        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }
    
    /**
     * Mark a visit as invalid manually
     */
    public function markInvalid(Request $request, $visitId)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);
        
        $visit = Visit::findOrFail($visitId);
        $user = $request->user();
        
        // Verify access
        if ($user->site_id && $visit->site_id !== $user->site_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para esta visita'
            ], 403);
        }
        
        $visit->update([
            'is_invalid' => true,
            'invalid_reason' => $request->reason,
            'status' => 'cancelled',
            'updated_by' => $user->id
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Visita marcada como inválida',
            'visit' => $visit->load(['department', 'site', 'updatedBy'])
        ]);
    }
}
