<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $organizer = User::factory()->create([
            'name' => 'Organizador Test',
            'email' => 'organizador@test.com',
            'password' => Hash::make('password123'),
            'is_organizer' => true,
        ]);

        $asistente = User::factory()->create([
            'name' => 'Asistente Test',
            'email' => 'asistente@test.com',
            'password' => Hash::make('password123'),
            'is_organizer' => false,
        ]);

        $categories = ['Conciertos', 'Talleres', 'Meetups', 'Conferencias', 'Jornadas Deportivas'];
        foreach ($categories as $catName) {
            Category::factory()->create([
                'name' => $catName,
                'slug' => \Illuminate\Support\Str::slug($catName)
            ]);
        }

        $allCategories = Category::all();
        $randomUsers = User::factory(10)->create();

        foreach ($allCategories as $category) {
            Event::factory(3)->create([
                'organizer_id' => $organizer->id,
                'category_id' => $category->id,
                'status' => 'upcoming'
            ]);
        }

        $upcomingEvents = Event::take(3)->get();
        foreach ($upcomingEvents as $event) {
            Registration::factory()->create([
                'user_id' => $asistente->id,
                'event_id' => $event->id,
                'checked_in' => false
            ]);
        }

        $pastEvent = Event::factory()->create([
            'title' => 'Evento Concluido de Rock',
            'organizer_id' => $organizer->id,
            'category_id' => $allCategories->first()->id,
            'status' => 'past',
            'date' => now()->subDays(5)
        ]);

        Registration::factory()->create([
            'user_id' => $asistente->id,
            'event_id' => $pastEvent->id,
            'checked_in' => true
        ]);

        Review::factory()->create([
            'user_id' => $asistente->id,
            'event_id' => $pastEvent->id,
            'rating' => 5,
            'comment' => '¡Un evento espectacular, la organización estuvo brillante!'
        ]);
    }
}