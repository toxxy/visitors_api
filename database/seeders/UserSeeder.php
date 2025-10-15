<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Site;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Admin Master
        User::create([
            'name' => 'Admin Master',
            'email' => 'admin@novolex.com',
            'password' => Hash::make('password'),
            'role' => 'admin_master',
        ]);

        // Admin Site para Rio Bravo
        $rioBravo = Site::where('name', 'Rio Bravo')->first();
        User::create([
            'name' => 'Admin Rio Bravo',
            'email' => 'admin.riobravo@novolex.com',
            'password' => Hash::make('password'),
            'role' => 'admin_site',
            'site_id' => $rioBravo->id,
        ]);

        // Admin Site para Brownsville
        $brownsville = Site::where('name', 'Brownsville')->first();
        User::create([
            'name' => 'Admin Brownsville',
            'email' => 'admin.brownsville@novolex.com',
            'password' => Hash::make('password'),
            'role' => 'admin_site',
            'site_id' => $brownsville->id,
        ]);

        // Security
        User::create([
            'name' => 'Seguridad Principal',
            'email' => 'security@novolex.com',
            'password' => Hash::make('password'),
            'role' => 'security',
        ]);

        // Manager para HR en Rio Bravo
        $hrRioBravo = Department::where('name', 'Recursos Humanos')->where('site_id', $rioBravo->id)->first();
        User::create([
            'name' => 'Manager HR Rio Bravo',
            'email' => 'hr.manager.riobravo@novolex.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'site_id' => $rioBravo->id,
            'department_id' => $hrRioBravo->id,
        ]);

        // Manager para Programación en Brownsville
        $progBrownsville = Department::where('name', 'Programación')->where('site_id', $brownsville->id)->first();
        User::create([
            'name' => 'Manager Programación Brownsville',
            'email' => 'prog.manager.brownsville@novolex.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'site_id' => $brownsville->id,
            'department_id' => $progBrownsville->id,
        ]);
    }
}
