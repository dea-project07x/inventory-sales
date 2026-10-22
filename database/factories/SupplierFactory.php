<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'PT ' . fake()->unique()->company(),
            'phone' => '08' . fake()->numerify('##########'),
            'email' => fake()->unique()->safeEmail(),
        ];
    }
}
