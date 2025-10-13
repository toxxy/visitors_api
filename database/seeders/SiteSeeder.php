<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Site;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = [
            [
                'name' => 'Rio Bravo',
                'location' => 'Rio Bravo, Texas',
                'address' => 'Rio Bravo Industrial Park',
                'active' => true
            ],
            [
                'name' => 'Brownsville',
                'location' => 'Brownsville, Texas',
                'address' => 'Brownsville Business Center',
                'active' => true
            ],
            [
                'name' => 'Katy', 
                'location' => 'Katy, Texas',
                'address' => 'Katy Corporate Plaza',
                'active' => true
            ]
        ];

        foreach ($sites as $site) {
            Site::create($site);
        }
    }
}
