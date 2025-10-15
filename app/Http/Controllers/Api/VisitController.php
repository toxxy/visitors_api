<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Notifications\VisitConfirmationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class VisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Visit::with(['department', 'site']);
        
        if ($request->has('site_id')) {
            $query->where('site_id', $request->site_id);
        }
        
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $visits = $query->orderBy('scheduled_at', 'desc')->get();
        return response()->json($visits);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'visitor_name' => 'required|string|max:255',
            'visitor_email' => 'nullable|email|max:255',
            'visitor_phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'purpose' => 'required|string|max:500',
            'department_id' => 'required|exists:departments,id',
            'site_id' => 'required|exists:sites,id',
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string'
        ]);

        $visit = Visit::create($validated);
        $visit->load(['department', 'site']);

        // Enviar confirmación por email si el visitante proporcionó un email
        if ($validated['visitor_email']) {
            try {
                \Log::info('Attempting to send visit confirmation email', [
                    'visit_id' => $visit->id,
                    'visitor_email' => $validated['visitor_email'],
                    'from_address' => config('mail.from.address'),
                    'smtp_host' => config('mail.mailers.smtp.host')
                ]);

                Notification::route('mail', $validated['visitor_email'])
                    ->notify(new VisitConfirmationNotification($visit));
                
                // Log exitoso
                \Log::info('Visit confirmation email sent successfully', [
                    'visit_id' => $visit->id,
                    'visitor_email' => $validated['visitor_email'],
                    'timestamp' => now()->toISOString()
                ]);
            } catch (\Exception $e) {
                // Log error pero no fallar la creación de la visita
                \Log::error('Failed to send visit confirmation email', [
                    'visit_id' => $visit->id,
                    'visitor_email' => $validated['visitor_email'],
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        return response()->json([
            'visit' => $visit,
            'message' => $validated['visitor_email'] 
                ? 'Visita programada exitosamente. Se ha enviado una confirmación por email.'
                : 'Visita programada exitosamente.',
            'email_sent' => (bool) $validated['visitor_email']
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Visit $visit)
    {
        return response()->json($visit->load(['department', 'site']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Visit $visit)
    {
        $validated = $request->validate([
            'visitor_name' => 'string|max:255',
            'visitor_email' => 'nullable|email|max:255',
            'visitor_phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'purpose' => 'string|max:500',
            'department_id' => 'exists:departments,id',
            'site_id' => 'exists:sites,id',
            'scheduled_at' => 'date|after:now',
            'arrived_at' => 'nullable|date',
            'departed_at' => 'nullable|date',
            'status' => 'in:scheduled,arrived,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        $visit->update($validated);
        return response()->json($visit->load(['department', 'site']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visit $visit)
    {
        $visit->delete();
        return response()->json(null, 204);
    }
}
