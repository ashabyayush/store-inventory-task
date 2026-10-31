<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->words(3, true),
            'code' => strtoupper(fake()->unique()->bothify('???-###')),
            'price' => fake()->randomFloat(2, 20, 1000),
            'tax_percent' => fake()->randomElement([0, 5, 12, 18]),
            'stock' => fake()->numberBetween(0, 100),
        ];
    }
}