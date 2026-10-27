<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 1, 100000),
            'category' => fake()->word(),
            'brand' => fake()->company(),
            'is_on_sale' => false,
            'discount_percent' => 0,
            'company_profile' => fake()->company(),
            'description' => fake()->sentence(),
            'image' => ['products/'.fake()->uuid().'.jpg'],
        ];
    }
}
