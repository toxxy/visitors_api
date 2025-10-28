<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\User;
use App\Notifications\VisitConfirmationNotification;
use App\Notifications\UnplannedVisitAlertNotification;
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
        // Base rules
        $rules = [
            'visitor_name' => 'required|string|max:255',
            'visitor_email' => 'nullable|email|max:255',
            'visitor_phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'purpose' => 'required|string|max:500',
            // department required unless unplanned
            'department_id' => 'required_unless:is_unplanned,true|exists:departments,id',
            'site_id' => 'required|exists:sites,id',
            'scheduled_at' => 'required|date',
            'notes' => 'nullable|string',
            'is_unplanned' => 'sometimes|boolean',
            'visit_to' => 'nullable|string|max:255',
        ];

        // Only enforce future date when it's a planned visit
        if (!$request->boolean('is_unplanned')) {
            // Allow scheduling at the moment of creation (>= now)
            $rules['scheduled_at'] .= '|after_or_equal:now';
        }

    $validated = $request->validate($rules);

        // Normalize boolean
        $validated['is_unplanned'] = $request->boolean('is_unplanned');
        // For unplanned visits, force scheduled_at to now
        if ($validated['is_unplanned']) {
            $validated['scheduled_at'] = now();
            // Ensure department_id is null when unplanned
            $validated['department_id'] = null;
        }

        $visit = Visit::create($validated);
        $visit->load(['department', 'site']);

        // Notify visitor
        if (!empty($validated['visitor_email'])) {
            try {
                Notification::route('mail', $validated['visitor_email'])
                    ->notify(new VisitConfirmationNotification($visit));
            } catch (\Throwable $e) {
                \Log::error('Failed to send visit confirmation email', [
                    'visit_id' => $visit->id,
                    'visitor_email' => $validated['visitor_email'] ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Notify managers (opt-in) if unplanned
        if (!empty($validated['is_unplanned'])) {
            $managers = User::where('role', 'manager')
                ->where('site_id', $visit->site_id)
                ->where('notify_unplanned', true)
                ->get();

            foreach ($managers as $manager) {
                try {
                    $manager->notify(new UnplannedVisitAlertNotification($visit));
                } catch (\Throwable $e) {
                    \Log::warning('Failed to notify manager for unplanned visit', [
                        'manager_id' => $manager->id,
                        'visit_id' => $visit->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'visit' => $visit,
            'message' => !empty($validated['visitor_email'])
                ? 'Visita programada exitosamente. Se ha enviado una confirmación al visitante.'
                : 'Visita programada exitosamente.',
            'email_sent' => (bool) ($validated['visitor_email'] ?? false),
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
            'department_id' => 'nullable|exists:departments,id',
            'site_id' => 'exists:sites,id',
            // Allow updating scheduled_at to now or later
            'scheduled_at' => 'date|after_or_equal:now',
            'arrived_at' => 'nullable|date',
            'departed_at' => 'nullable|date',
            'status' => 'in:scheduled,confirmed,arrived,completed,cancelled',
            'notes' => 'nullable|string',
            'is_unplanned' => 'sometimes|boolean',
            'visit_to' => 'nullable|string|max:255',
        ]);

        if ($request->has('is_unplanned')) {
            $validated['is_unplanned'] = $request->boolean('is_unplanned');
        }

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
