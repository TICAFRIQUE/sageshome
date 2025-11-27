<?php

namespace Database\Seeders;

use App\Models\Residence;
use App\Models\ResidenceImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResidenceSeeder extends Seeder
{
    public function run(): void
    {
        $residences = [
            [
                'name' => 'Villa Opale Ocean View',
                'description' => 'Magnifique studio moderne avec vue panoramique sur l\'océan, parfait pour un séjour romantique à Dakar.',
                'full_description' => 'Ce studio d\'exception offre une vue imprenable sur l\'océan Atlantique. Situé dans le quartier huppé des Almadies, il combine modernité et confort avec des finitions haut de gamme. L\'espace optimisé comprend un lit king-size, une kitchenette entièrement équipée, un salon avec canapé-lit et une terrasse privée. Idéal pour les couples en quête d\'un cadre idyllique.',
                'type' => 'studio_1ch',
                'capacity' => 2,
                'price_per_night' => 35000,
                'amenities' => ['wifi', 'climatiseur', 'cuisiniere', 'refrigerateur', 'micro_onde', 'bouilloire', 'couverts', 'canal_plus', 'chauffe_eau', 'menage', 'securite', 'arrivee_autonome'],
                'address' => 'Route des Almadies, Pointe des Almadies, Dakar, Sénégal',
                'latitude' => 14.7208,
                'longitude' => -17.5108,
                'is_available' => true,
                'is_featured' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=600&fit=crop', // Luxury hotel room ocean view
                    'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=800&h=600&fit=crop', // Modern luxury bedroom
                    'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?w=800&h=600&fit=crop', // Ocean view terrace
                    'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=800&h=600&fit=crop', // Luxury bathroom
                ]
            ],
            [
                'name' => 'Résidence Emeraude Garden',
                'description' => 'Studio spacieux avec jardin privé et terrasse, situé dans un écrin de verdure au cœur de Dakar.',
                'full_description' => 'Un studio lumineux et spacieux niché dans un jardin tropical luxuriant. Cette résidence offre une expérience unique alliant intimité et nature. Équipé d\'une cuisine moderne, d\'une chambre confortable et d\'une terrasse donnant sur le jardin privé. Parfait pour se ressourcer tout en restant proche du centre-ville.',
                'type' => 'studio_1ch',
                'capacity' => 2,
                'price_per_night' => 28000,
                'amenities' => ['wifi', 'climatiseur', 'cuisiniere', 'four', 'refrigerateur', 'micro_onde', 'ustensiles', 'couverts', 'chauffe_eau', 'menage', 'securite', 'arrivee_autonome'],
                'address' => 'Mermoz-Sacré-Cœur, Dakar, Sénégal',
                'latitude' => 14.7128,
                'longitude' => -17.4647,
                'is_available' => true,
                'is_featured' => false,
                'images' => [
                    'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800&h=600&fit=crop', // Modern house exterior with garden
                    'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&h=600&fit=crop', // Cozy studio interior
                    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&h=600&fit=crop', // Garden terrace
                    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800&h=600&fit=crop', // Modern kitchen
                ]
            ],
            [
                'name' => 'Appartement Saphir Lagune',
                'description' => 'Élégant appartement 1 chambre avec vue sur la lagune, décoré avec goût et entièrement équipé.',
                'full_description' => 'Cet appartement raffiné d\'une chambre offre une vue exceptionnelle sur la lagune de Ngor. L\'intérieur, décoré dans un style contemporain africain, comprend une chambre spacieuse, un salon lumineux, une cuisine moderne et une terrasse avec vue. Idéal pour les voyageurs en quête d\'authenticité et de confort.',
                'type' => 'studio_1ch',
                'capacity' => 3,
                'price_per_night' => 42000,
                'amenities' => ['wifi', 'climatiseur', 'cuisiniere', 'four', 'micro_onde', 'refrigerateur', 'mixeur', 'ustensiles', 'couverts', 'canal_plus', 'chauffe_eau', 'menage', 'securite', 'arrivee_autonome', 'annulation_gratuite'],
                'address' => 'Ngor Virage, Dakar, Sénégal',
                'latitude' => 14.7530,
                'longitude' => -17.5151,
                'is_available' => true,
                'is_featured' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&h=600&fit=crop', // Luxury apartment living room
                    'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&h=600&fit=crop', // Bedroom with lagoon view
                    'https://images.unsplash.com/photo-1556912167-f556f1f39fdf?w=800&h=600&fit=crop', // Modern dining area
                    'https://images.unsplash.com/photo-1543071293-d91175a68672?w=800&h=600&fit=crop', // Terrace with water view
                ]
            ],
            [
                'name' => 'Villa Diamant Familiale',
                'description' => 'Spacieuse villa 2 chambres avec piscine privée, parfaite pour les familles ou groupes d\'amis.',
                'full_description' => 'Une villa familiale exceptionnelle avec deux chambres confortables, salon spacieux et piscine privée. Cette propriété combine l\'art de vivre sénégalais et le confort moderne. Elle dispose d\'un grand jardin, d\'une terrasse couverte et d\'une cuisine entièrement équipée. Idéale pour des vacances en famille mémorables.',
                'type' => 'appartement_2ch',
                'capacity' => 6,
                'price_per_night' => 65000,
                'amenities' => ['wifi', 'climatiseur', 'cuisiniere', 'four', 'micro_onde', 'refrigerateur', 'mixeur', 'ustensiles', 'couverts', 'bouilloire', 'canal_plus', 'chauffe_eau', 'menage', 'securite', 'piscine', 'arrivee_autonome', 'annulation_gratuite'],
                'address' => 'Virage, Dakar, Sénégal',
                'latitude' => 14.7392,
                'longitude' => -17.4963,
                'is_available' => true,
                'is_featured' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=800&h=600&fit=crop', // Luxury villa with pool
                    'https://images.unsplash.com/photo-1560448075-bb485b067938?w=800&h=600&fit=crop', // Family living room
                    'https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?w=800&h=600&fit=crop', // Master bedroom
                    'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=600&fit=crop', // Pool area
                ]
            ],
            [
                'name' => 'Penthouse Rubis Premium',
                'description' => 'Luxueux penthouse 2 chambres avec terrasse panoramique et vue à 360° sur Dakar.',
                'full_description' => 'Le summum du luxe avec ce penthouse d\'exception offrant une vue panoramique à 360° sur Dakar et l\'océan. Deux chambres master avec salle de bain privée, salon de prestige, cuisine gourmet et terrasse de 100m². Service concierge inclus pour un séjour inoubliable.',
                'type' => 'appartement_2ch',
                'capacity' => 4,
                'price_per_night' => 95000,
                'amenities' => ['wifi', 'climatiseur', 'cuisiniere', 'four', 'micro_onde', 'refrigerateur', 'mixeur', 'ustensiles', 'couverts', 'bouilloire', 'canal_plus', 'chauffe_eau', 'menage', 'securite', 'arrivee_autonome', 'annulation_gratuite'],
                'address' => 'Mamelles, Ouakam, Dakar, Sénégal',
                'latitude' => 14.6965,
                'longitude' => -17.4616,
                'is_available' => true,
                'is_featured' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&h=600&fit=crop', // Penthouse view
                    'https://images.unsplash.com/photo-1600607687644-c7171b42498f?w=800&h=600&fit=crop', // Luxury penthouse interior
                    'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=800&h=600&fit=crop', // Panoramic terrace
                    'https://images.unsplash.com/photo-1600607688969-a5bfcd646154?w=800&h=600&fit=crop', // Premium kitchen
                ]
            ],
            [
                'name' => 'Villa Perle Royale',
                'description' => 'Majestueuse villa 3 chambres avec piscine, jardin tropical et service de conciergerie.',
                'full_description' => 'Une villa de prestige dans un écrin tropical exceptionnel. Trois chambres luxueuses, salon cathédrale, cuisine gastronomique, piscine à débordement et jardin paysagé. Service de conciergerie 24h/24, chef à domicile sur demande. L\'excellence à l\'état pur pour un séjour royal.',
                'type' => 'appartement_3ch',
                'capacity' => 8,
                'price_per_night' => 120000,
                'amenities' => ['wifi', 'climatiseur', 'cuisiniere', 'four', 'micro_onde', 'refrigerateur', 'mixeur', 'ustensiles', 'couverts', 'bouilloire', 'canal_plus', 'chauffe_eau', 'menage', 'securite', 'piscine', 'arrivee_autonome', 'annulation_gratuite'],
                'address' => 'Yoff Tonghor, Dakar, Sénégal',
                'latitude' => 14.7583,
                'longitude' => -17.4925,
                'is_available' => true,
                'is_featured' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?w=800&h=600&fit=crop', // Luxury villa exterior
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&h=600&fit=crop', // Luxury living room
                    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800&h=600&fit=crop', // Master bedroom suite
                    'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=600&fit=crop', // Infinity pool
                ]
            ],
            [
                'name' => 'Résidence Topaze Moderne',
                'description' => 'Villa contemporaine 3 chambres avec design moderne, idéale pour les groupes exigeants.',
                'full_description' => 'Une architecture contemporaine remarquable pour cette villa de 3 chambres aux lignes épurées. Conçue par un architecte de renom, elle offre des espaces généreux et lumineux, une cuisine design, une piscine moderne et un jardin zen. Pour les amateurs de design et de modernité.',
                'type' => 'appartement_3ch',
                'capacity' => 7,
                'price_per_night' => 85000,
                'amenities' => ['wifi', 'climatiseur', 'cuisiniere', 'four', 'micro_onde', 'refrigerateur', 'mixeur', 'ustensiles', 'couverts', 'bouilloire', 'canal_plus', 'chauffe_eau', 'menage', 'securite', 'piscine', 'arrivee_autonome', 'annulation_gratuite'],
                'address' => 'Les Maristes, Dakar, Sénégal',
                'latitude' => 14.7245,
                'longitude' => -17.4580,
                'is_available' => true,
                'is_featured' => false,
                'images' => [
                    'https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=800&h=600&fit=crop', // Modern villa architecture
                    'https://images.unsplash.com/photo-1600566752355-35792bedcfea?w=800&h=600&fit=crop', // Contemporary interior
                    'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=600&fit=crop', // Modern bedroom
                    'https://images.unsplash.com/photo-1600607688960-ac5c4d5e2020?w=800&h=600&fit=crop', // Designer kitchen
                ]
            ],
            [
                'name' => 'Villa Ambre Traditionnelle',
                'description' => 'Charmante villa traditionnelle rénovée, mélange parfait entre authenticité sénégalaise et confort moderne.',
                'full_description' => 'Une villa traditionnelle sénégalaise magnifiquement rénovée qui préserve l\'âme du patrimoine architectural local tout en offrant le confort moderne. Trois chambres décorées avec des œuvres d\'art local, salon traditionnel, terrasse ombragée et jardin aux essences locales. Une immersion culturelle authentique.',
                'type' => 'appartement_3ch',
                'capacity' => 6,
                'price_per_night' => 75000,
                'amenities' => ['wifi', 'climatiseur', 'cuisiniere', 'four', 'micro_onde', 'refrigerateur', 'ustensiles', 'couverts', 'chauffe_eau', 'menage', 'securite', 'arrivee_autonome', 'annulation_gratuite'],
                'address' => 'Plateau, Dakar, Sénégal',
                'latitude' => 14.6937,
                'longitude' => -17.4441,
                'is_available' => true,
                'is_featured' => false,
                'images' => [
                    'https://images.unsplash.com/photo-1600585154084-4e2803814758?w=800&h=600&fit=crop', // Traditional architecture
                    'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?w=800&h=600&fit=crop', // Traditional interior
                    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&h=600&fit=crop', // Cultural decor
                    'https://images.unsplash.com/photo-1600607688669-675bac4a8e3b?w=800&h=600&fit=crop', // Traditional courtyard
                ]
            ]
        ];

        foreach ($residences as $residenceData) {
            // Séparer les images des autres données
            $images = $residenceData['images'];
            unset($residenceData['images']);
            
            // Créer la résidence
            $residence = Residence::create($residenceData);

            // Télécharger et sauvegarder les images
            foreach ($images as $index => $imageUrl) {
                try {
                    // Télécharger l'image
                    $imageContent = Http::get($imageUrl)->body();
                    
                    // Générer un nom de fichier unique
                    $filename = $residence->id . '_' . ($index + 1) . '_' . Str::random(8) . '.jpg';
                    $path = 'residences/' . $filename;
                    
                    // Sauvegarder dans le storage public
                    Storage::disk('public')->put($path, $imageContent);
                    
                    // Créer l'enregistrement de l'image
                    ResidenceImage::create([
                        'residence_id' => $residence->id,
                        'image_path' => $path,
                        'alt_text' => $residence->name . ' - Image ' . ($index + 1),
                        'is_primary' => $index === 0, // La première image est principale
                        'sort_order' => $index,
                    ]);
                    
                    echo "✅ Image téléchargée pour {$residence->name}: {$filename}\n";
                    
                } catch (\Exception $e) {
                    echo "❌ Erreur lors du téléchargement de l'image pour {$residence->name}: " . $e->getMessage() . "\n";
                    
                    // En cas d'erreur, créer une image par défaut
                    ResidenceImage::create([
                        'residence_id' => $residence->id,
                        'image_path' => 'residences/placeholder.jpg',
                        'alt_text' => $residence->name . ' - Image ' . ($index + 1),
                        'is_primary' => $index === 0,
                        'sort_order' => $index,
                    ]);
                }
            }
        }
        
        echo "🎉 Seeder terminé avec succès !\n";
    }
}