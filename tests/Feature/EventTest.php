<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;


    public function test_event_status_updates_to_sold_out_automatically(): void
    {
        $organizer = User::factory()->create(['is_organizer' => true]);
        $category = Category::factory()->create();
        
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'category_id' => $category->id,
            'capacity' => 1,
            'status' => 'upcoming'
        ]);

        $asistente = User::factory()->create();
        Sanctum::actingAs($asistente);

        $response = $this->postJson("/api/v1/events/{$event->id}/register");
        $response->assertStatus(201);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'status' => 'sold_out'
        ]);

        $otroAsistente = User::factory()->create();
        Sanctum::actingAs($otroAsistente);

        $responseError = $this->postJson("/api/v1/events/{$event->id}/register");
        $responseError->assertStatus(422)
            ->assertJson([
                'success' => false,
                'data' => null
            ]);
    }

    public function test_user_cannot_leave_duplicate_reviews(): void
    {
        $organizer = User::factory()->create(['is_organizer' => true]);
        $category = Category::factory()->create();
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'category_id' => $category->id,
            'status' => 'past'
        ]);

        $asistente = User::factory()->create();
        Sanctum::actingAs($asistente);

        Registration::create([
            'user_id' => $asistente->id,
            'event_id' => $event->id,
            'unique_code' => 'TKT-12345',
            'checked_in' => true
        ]);

        $response1 = $this->postJson("/api/v1/events/{$event->id}/reviews", [
            'rating' => 5,
            'comment' => '¡Increíble!'
        ]);
        $response1->assertStatus(201);

        $response2 = $this->postJson("/api/v1/events/{$event->id}/reviews", [
            'rating' => 4,
            'comment' => 'Intento duplicar mi opinión.'
        ]);
        
        $response2->assertStatus(422)
            ->assertJson([
                'success' => false,
                'data' => null
            ]);
    }
}