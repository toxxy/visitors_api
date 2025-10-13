<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Site;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Compras' => 'Departamento de adquisiciones y proveedores',
            'Programación' => 'Departamento de desarrollo y sistemas',
            'Calidad' => 'Control de calidad y aseguramiento',
            'Gerencia' => 'Dirección general y administración',
            'Recursos Humanos' => 'Gestión de personal y nómina',
            'Finanzas' => 'Contabilidad y tesorería',
            'Producción' => 'Operaciones y manufactura',
            'Logística' => 'Almacén y distribución',
            'Mantenimiento' => 'Servicios técnicos y mantenimiento',
            'Ventas' => 'Comercialización y atención al cliente'
        ];

        $sites = Site::all();

        foreach ($sites as $site) {
            foreach ($departments as $name => $description) {
                Department::create([
                    'name' => $name,
                    'description' => $description,
                    'site_id' => $site->id,
                    'active' => true
                ]);
            }
        }
    }
}
