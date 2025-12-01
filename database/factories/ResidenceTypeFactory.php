<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResidenceType>
 */
class ResidenceTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['Studio', 'Appartement', 'Villa', 'Duplex', 'Loft', 'Penthouse'];
        $type = fake()->randomElement($types);
        
        return [
            'name' => $type . ' ' . fake()->numberBetween(1, 3) . ' chambre' . (fake()->numberBetween(1, 3) > 1 ? 's' : ''),
            'description' => fake()->paragraph(2),
            'min_capacity' => fake()->numberBetween(1, 2),
            'max_capacity' => fake()->numberBetween(4, 12),
            'is_active' => fake()->boolean(90), // 90% de chance d'être actif
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
    
    /**
     * Indicate that the residence type is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
    
    /**
     * Indicate that the residence type is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
