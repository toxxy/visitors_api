<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Http\Request;

class VisitConfirmationController extends Controller
{
    /**
     * Generate printable confirmation for a visit
     */
    public function generateConfirmation(Visit $visit)
    {
        // Cargar relaciones necesarias
        $visit->load(['department', 'site']);
        
        // Retornar la vista de confirmación
        return view('emails.visit-confirmation', compact('visit'));
    }
    

}