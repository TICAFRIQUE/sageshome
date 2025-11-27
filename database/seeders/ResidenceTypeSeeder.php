<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ResidenceType;

class ResidenceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'studio',
                'display_name' => 'Studio',
                'description' => 'Espace unique combinant salon, chambre et kitchenette',
                'min_capacity' => 1,
                'max_capacity' => 2,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'studio_1ch',
                'display_name' => 'Studio 1 chambre',
                'description' => 'Studio avec une chambre séparée',
                'min_capacity' => 2,
                'max_capacity' => 3,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'appartement_1ch',
                'display_name' => 'Appartement 1 chambre',
                'description' => 'Appartement avec une chambre, salon et cuisine séparés',
                'min_capacity' => 2,
                'max_capacity' => 4,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'appartement_2ch',
                'display_name' => 'Appartement 2 chambres',
                'description' => 'Appartement spacieux avec deux chambres',
                'min_capacity' => 3,
                'max_capacity' => 6,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'appartement_3ch',
                'display_name' => 'Appartement 3 chambres',
                'description' => 'Grand appartement familial avec trois chambres',
                'min_capacity' => 4,
                'max_capacity' => 8,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'villa',
                'display_name' => 'Villa',
                'description' => 'Villa indépendante avec jardin privé',
                'min_capacity' => 4,
                'max_capacity' => 12,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'penthouse',
                'display_name' => 'Penthouse',
                'description' => 'Appartement de luxe au dernier étage avec terrasse',
                'min_capacity' => 2,
                'max_capacity' => 8,
                'is_active' => true,
                'sort_order' => 7,
            ],
        ];

        foreach ($types as $type) {
            ResidenceType::create($type);
        }
    }
}
