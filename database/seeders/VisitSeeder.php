<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Visit;
use App\Models\Site;
use App\Models\Department;
use Carbon\Carbon;

class VisitSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $sites = Site::all();
        
        foreach ($sites as $site) {
            $departments = Department::where('site_id', $site->id)->get();
            
            if ($departments->isEmpty()) continue;
            
            // Crear visitas de hoy
            for ($i = 1; $i <= 5; $i++) {
                Visit::create([
                    'visitor_name' => 'Visitante ' . $i . ' - ' . $site->name,
                    'visitor_email' => 'visitor' . $i . '@' . strtolower(str_replace(' ', '', $site->name)) . '.com',
                    'visitor_phone' => '555-000' . $i . '00' . $site->id,
                    'company' => 'Empresa ' . $i,
                    'purpose' => 'Reunión de negocios',
                    'department_id' => $departments->random()->id,
                    'site_id' => $site->id,
                    'scheduled_at' => Carbon::today()->addHours(8 + $i),
                    'status' => 'scheduled',
                    'check_in_count' => 0,
                    'check_out_count' => 0,
                    'is_invalid' => false
                ]);
            }
            
            // Crear algunas visitas con check-in
            for ($i = 6; $i <= 8; $i++) {
                $scheduledTime = Carbon::today()->addHours(7 + $i);
                $checkedInTime = $scheduledTime->copy()->addMinutes(15);
                
                Visit::create([
                    'visitor_name' => 'Visitante ' . $i . ' - ' . $site->name,
                    'visitor_email' => 'visitor' . $i . '@' . strtolower(str_replace(' ', '', $site->name)) . '.com',
                    'visitor_phone' => '555-000' . $i . '00' . $site->id,
                    'company' => 'Empresa ' . $i,
                    'purpose' => 'Visita técnica',
                    'department_id' => $departments->random()->id,
                    'site_id' => $site->id,
                    'scheduled_at' => $scheduledTime,
                    'arrived_at' => $checkedInTime,
                    'checked_in_at' => $checkedInTime,
                    'status' => 'arrived',
                    'check_in_count' => 1,
                    'check_out_count' => 0,
                    'is_invalid' => false
                ]);
            }
            
            // Crear algunas visitas completadas
            for ($i = 9; $i <= 10; $i++) {
                $scheduledTime = Carbon::today()->addHours(6 + $i);
                $checkedInTime = $scheduledTime->copy()->addMinutes(10);
                $checkedOutTime = $checkedInTime->copy()->addHours(2);
                
                Visit::create([
                    'visitor_name' => 'Visitante ' . $i . ' - ' . $site->name,
                    'visitor_email' => 'visitor' . $i . '@' . strtolower(str_replace(' ', '', $site->name)) . '.com',
                    'visitor_phone' => '555-000' . $i . '00' . $site->id,
                    'company' => 'Empresa ' . $i,
                    'purpose' => 'Auditoría',
                    'department_id' => $departments->random()->id,
                    'site_id' => $site->id,
                    'scheduled_at' => $scheduledTime,
                    'arrived_at' => $checkedInTime,
                    'departed_at' => $checkedOutTime,
                    'checked_in_at' => $checkedInTime,
                    'checked_out_at' => $checkedOutTime,
                    'status' => 'completed',
                    'check_in_count' => 1,
                    'check_out_count' => 1,
                    'is_invalid' => false
                ]);
            }
            
            // Crear una visita inválida
            Visit::create([
                'visitor_name' => 'Visitante Inválido - ' . $site->name,
                'visitor_email' => 'invalid@' . strtolower(str_replace(' ', '', $site->name)) . '.com',
                'visitor_phone' => '555-9999' . $site->id,
                'company' => 'Empresa Inválida',
                'purpose' => 'Visita no autorizada',
                'department_id' => $departments->random()->id,
                'site_id' => $site->id,
                'scheduled_at' => Carbon::today()->addHours(14),
                'status' => 'cancelled',
                'check_in_count' => 0,
                'check_out_count' => 0,
                'is_invalid' => true,
                'invalid_reason' => 'Documentación inválida'
            ]);
        }
    }
}
