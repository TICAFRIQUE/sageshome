<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResidenceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $residenceTypes = [
            [
                'name' => 'Studio',
                'slug' => 'studio',
                'description' => 'Appartement compact avec une pièce principale combinant salon et chambre.',
                'min_capacity' => 1,
                'max_capacity' => 2,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Appartement 1 chambre',
                'slug' => 'appartement-1-chambre',
                'description' => 'Appartement avec une chambre séparée, salon et cuisine.',
                'min_capacity' => 2,
                'max_capacity' => 3,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Appartement 2 chambres',
                'slug' => 'appartement-2-chambres',
                'description' => 'Appartement spacieux avec deux chambres, salon et cuisine équipée.',
                'min_capacity' => 3,
                'max_capacity' => 5,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Appartement 3 chambres',
                'slug' => 'appartement-3-chambres',
                'description' => 'Grand appartement familial avec trois chambres et espaces de vie généreux.',
                'min_capacity' => 4,
                'max_capacity' => 7,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Villa',
                'slug' => 'villa',
                'description' => 'Maison individuelle avec jardin, parfaite pour les grands groupes et familles.',
                'min_capacity' => 6,
                'max_capacity' => 12,
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Duplex',
                'slug' => 'duplex',
                'description' => 'Logement sur deux niveaux offrant plus d\'intimité et d\'espace.',
                'min_capacity' => 4,
                'max_capacity' => 8,
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Penthouse',
                'slug' => 'penthouse',
                'description' => 'Appartement de luxe au dernier étage avec terrasse privée.',
                'min_capacity' => 2,
                'max_capacity' => 8,
                'sort_order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($residenceTypes as $type) {
            // Vérifier si le type existe déjà par nom
            $existingType = \App\Models\ResidenceType::where('name', $type['name'])->first();
            
            if (!$existingType) {
                \App\Models\ResidenceType::create($type);
                $this->command->info("Type de résidence créé: {$type['name']}");
            } else {
                $this->command->info("Type de résidence existe déjà: {$type['name']}");
            }
        }

        $this->command->info('Types de résidences créés avec succès !');
    }
}
