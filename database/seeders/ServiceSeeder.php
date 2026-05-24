<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'title' => 'Mechanical Work',
                'slug' => 'mechanical-work',
                'short_description' => 'Installation, repair, alignment, and mechanical project support.',
                'description' => 'Installation, alignment, mechanical repair, and equipment support for industrial operations.',
            ],
            [
                'title' => 'Electrical Work',
                'slug' => 'electrical-work',
                'short_description' => 'Electrical installation, maintenance, and field troubleshooting support.',
                'description' => 'Electrical installation, maintenance, troubleshooting, and project site support.',
            ],
            [
                'title' => 'Fabrication',
                'slug' => 'fabrication',
                'short_description' => 'Steel structure, equipment support, and workshop/site fabrication work.',
                'description' => 'Steel structure, equipment support, custom fabrication, and workshop/site fabrication work.',
            ],
            [
                'title' => 'Maintenance',
                'slug' => 'maintenance',
                'short_description' => 'Preventive and corrective maintenance for industrial facilities.',
                'description' => 'Preventive maintenance, corrective maintenance, shutdown support, and plant reliability work.',
            ],
            [
                'title' => 'Scaffolding',
                'slug' => 'scaffolding',
                'short_description' => 'Scaffolding manpower and access support for safe work areas.',
                'description' => 'Safe access solutions and scaffolding manpower for maintenance and construction activities.',
            ],
            [
                'title' => 'Manpower Supply',
                'slug' => 'manpower-supply',
                'short_description' => 'Skilled manpower support for project and shutdown activities.',
                'description' => 'Skilled manpower support for project execution, maintenance, and industrial site operations.',
            ],
            [
                'title' => 'Piping',
                'slug' => 'piping',
                'short_description' => 'Pipe fabrication, installation, modification, and maintenance.',
                'description' => 'Pipe fabrication, installation, modification, and maintenance for industrial systems.',
            ],
            [
                'title' => 'Civil Construction',
                'slug' => 'civil-construction',
                'short_description' => 'Civil, construction, and infrastructure support for project sites.',
                'description' => 'Civil works, construction support, foundations, structures, and project infrastructure.',
            ],
        ];

        foreach ($services as $index => $service) {
            Service::updateOrCreate(
                ['slug' => $service['slug']],
                array_merge($service, [
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ])
            );
        }
    }
}
