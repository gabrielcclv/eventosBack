<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'date' => $this->faker->dateTimeBetween('+1 week', '+2 months'),
            'city' => $this->faker->randomElement(['Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Bilbao']),
            'status' => 'upcoming',
            'image_url' => $this->faker->imageUrl(640, 480, 'events'),
            'capacity' => $this->faker->numberBetween(20, 150),
            'organizer_id' => User::factory(),
            'category_id' => Category::factory(),
        ];
    }
}