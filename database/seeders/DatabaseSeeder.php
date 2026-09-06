<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Services\AiService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with sample notes and embeddings.
     */
    public function run(): void
    {
        $aiService = new AiService();

        $sampleNotes = [
            [
                'title' => 'Laravel RESTful API Best Practices',
                'content' => 'When designing RESTful APIs in Laravel, always use Form Requests for strict validation, Eloquent API Resources for consistent output formatting, and HTTP status codes like 200 OK, 201 Created, 404 Not Found, and 422 Unprocessable Entity. Rate limiting via Throttle middleware prevents API abuse.',
                'tags' => ['laravel', 'api', 'backend', 'php'],
            ],
            [
                'title' => 'Machine Learning Vector Embeddings Explained',
                'content' => 'Vector embeddings transform high-dimensional unstructured data like text, images, or audio into dense numerical vectors in a continuous vector space. Cosine similarity calculates the dot product of normalized vectors to measure semantic proximity between search queries and notes.',
                'tags' => ['ai', 'machine-learning', 'embeddings', 'vector-search'],
            ],
            [
                'title' => 'Docker & Docker Compose Containerization Guide',
                'content' => 'Docker encapsulates microservices into isolated Linux containers. Docker Compose simplifies orchestrating multi-container environments combining PHP-FPM, Nginx web servers, MySQL relational databases, and Redis caching layers using a declarative docker-compose.yml setup.',
                'tags' => ['docker', 'devops', 'deployment', 'nginx'],
            ],
            [
                'title' => 'Database Indexing and SQL Query Optimization',
                'content' => 'Database performance heavily depends on proper B-tree indexing on frequently queried foreign keys and search columns. Avoiding SELECT * statements, leveraging prepared statements against SQL injection, and analyzing EXPLAIN plans reduces query latency drastically.',
                'tags' => ['database', 'mysql', 'sql', 'performance'],
            ],
            [
                'title' => 'Modern Glassmorphism UI & Modern CSS Layouts',
                'content' => 'Glassmorphism creates a sleek modern aesthetic using CSS backdrop-filter blur, semi-transparent frosted glass containers, subtle neon gradient borders, and smooth CSS micro-interactions. Dynamic UI feedback elevates user experience and engagement.',
                'tags' => ['frontend', 'css', 'design', 'ui-ux'],
            ],
            [
                'title' => 'Redis Caching for High-Performance Backend APIs',
                'content' => 'Redis acts as an ultra-fast in-memory key-value store for caching expensive AI summary results, database query responses, and session state. TTL expiration policies ensure cache invalidation while dramatically reducing database load.',
                'tags' => ['redis', 'caching', 'performance', 'backend'],
            ]
        ];

        foreach ($sampleNotes as $data) {
            $embedding = $aiService->generateEmbedding($data['title'] . ' ' . $data['content']);
            $summary = $aiService->generateSummary($data['content']);

            Note::create([
                'title' => $data['title'],
                'content' => $data['content'],
                'tags' => $data['tags'],
                'embedding' => $embedding,
                'summary' => $summary,
            ]);
        }
    }
}
