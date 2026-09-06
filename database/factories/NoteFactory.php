<?php

namespace Database\Factories;

use App\Models\Note;
use App\Services\AiService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Note>
 */
class NoteFactory extends Factory
{
    protected $model = Note::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(4);
        $content = $this->faker->paragraphs(2, true);
        
        $aiService = new AiService();
        $embedding = $aiService->generateEmbedding($title . ' ' . $content);

        return [
            'title' => rtrim($title, '.'),
            'content' => $content,
            'tags' => $this->faker->randomElements(['laravel', 'php', 'ai', 'backend', 'database', 'frontend', 'docker'], rand(1, 3)),
            'embedding' => $embedding,
            'summary' => null,
        ];
    }
}
