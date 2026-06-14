<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement(['Conciertos', 'Talleres', 'Meetups', 'Conferencias', 'Deportes']);
        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}