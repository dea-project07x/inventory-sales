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
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####-??')),
            'name' => fake()->unique()->words(3, true),
            'purchase_price' => 8000,
            'selling_price' => 10000,
            'stock' => 20,
            'minimum_stock' => 5,
        ];
    }
}
