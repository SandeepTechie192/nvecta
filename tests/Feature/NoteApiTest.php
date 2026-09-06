<?php

namespace Tests\Feature;

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fetch_paginated_notes_list()
    {
        Note::factory()->count(12)->create();

        $response = $this->getJson('/api/notes?page=1&limit=5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'content', 'tags', 'created_at', 'updated_at']
                ],
                'meta' => ['current_page', 'limit', 'total', 'last_page', 'has_more']
            ]);

        $this->assertEquals(5, count($response->json('data')));
        $this->assertEquals(12, $response->json('meta.total'));
    }

    /** @test */
    public function it_can_create_a_note_and_generate_embedding()
    {
        $payload = [
            'title' => 'Test AI Integration Note',
            'content' => 'Artificial intelligence and neural networks are revolutionizing software development.',
            'tags' => ['ai', 'testing', 'laravel'],
        ];

        $response = $this->postJson('/api/notes', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Test AI Integration Note');

        $this->assertDatabaseHas('notes', ['title' => 'Test AI Integration Note']);
        
        $note = Note::where('title', 'Test AI Integration Note')->first();
        $this->assertIsArray($note->embedding);
        $this->assertNotEmpty($note->embedding);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_note()
    {
        $response = $this->postJson('/api/notes', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content']);
    }

    /** @test */
    public function it_can_fetch_a_single_note()
    {
        $note = Note::factory()->create(['title' => 'Unique Note Title']);

        $response = $this->getJson("/api/notes/{$note->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Unique Note Title');
    }

    /** @test */
    public function it_returns_404_for_non_existent_note()
    {
        $response = $this->getJson('/api/notes/99999');

        $response->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    /** @test */
    public function it_can_update_a_note()
    {
        $note = Note::factory()->create(['title' => 'Original Title']);

        $response = $this->putJson("/api/notes/{$note->id}", [
            'title' => 'Updated Note Title',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Note Title');

        $this->assertDatabaseHas('notes', ['id' => $note->id, 'title' => 'Updated Note Title']);
    }

    /** @test */
    public function it_can_delete_a_note()
    {
        $note = Note::factory()->create();

        $response = $this->deleteJson("/api/notes/{$note->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    /** @test */
    public function it_can_generate_an_ai_summary_for_a_note()
    {
        $note = Note::factory()->create([
            'content' => 'Laravel is a web application framework with expressive, elegant syntax. It aims to make the development process enjoyable.',
        ]);

        $response = $this->postJson("/api/notes/{$note->id}/summary");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['summary', 'note_id']);

        $this->assertNotNull($note->fresh()->summary);
    }

    /** @test */
    public function it_can_perform_semantic_vector_search()
    {
        Note::create([
            'title' => 'Deep Learning Neural Networks',
            'content' => 'Neural networks learn representations from big datasets using backpropagation algorithms.',
            'tags' => ['ai', 'deep-learning'],
        ]);

        Note::create([
            'title' => 'Baking Italian Pizza',
            'content' => 'Mix flour, yeast, olive oil, and water to knead dough for authentic Neapolitan pizza.',
            'tags' => ['cooking', 'food'],
        ]);

        $response = $this->getJson('/api/notes/search?query=neural+networks&limit=5');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $results = $response->json('data');
        $this->assertNotEmpty($results);
        $this->assertEquals('Deep Learning Neural Networks', $results[0]['title']);
    }
}
