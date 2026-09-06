<?php

namespace App\Services;

use App\Models\Note;
use Illuminate\Support\Collection;

class VectorSearchService
{
    protected AiService $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Perform AI-powered semantic search across notes.
     *
     * @param string $query
     * @param int $limit
     * @param float $minSimilarity
     * @return Collection
     */
    /**
     * Perform AI-powered semantic and hybrid search across notes.
     *
     * @param string $query
     * @param int $limit
     * @param float $minSimilarity
     * @return Collection
     */
    public function search(string $query, int $limit = 10, float $minSimilarity = 0.01): Collection
    {
        $rawQuery = trim($query);
        if ($rawQuery === '') {
            return collect();
        }

        $lowercaseQuery = mb_strtolower($rawQuery);

        // Generate embedding vector for the search query
        $queryVector = $this->aiService->generateEmbedding($rawQuery);

        // Fetch notes with embeddings or generate missing ones
        $notes = Note::all();

        $results = $notes->map(function (Note $note) use ($queryVector, $lowercaseQuery) {
            $embedding = $note->embedding;

            // Generate embedding on-the-fly if missing
            if (empty($embedding)) {
                $embedding = $this->aiService->generateEmbedding($note->title . ' ' . $note->content);
                $note->embedding = $embedding;
                $note->saveQuietly();
            }

            // 1. Vector Cosine Similarity
            $vectorScore = $this->cosineSimilarity($queryVector, $embedding);

            // 2. Exact/Partial Text Match Score
            $textScore = 0.0;
            $titleLower = mb_strtolower($note->title ?? '');
            $contentLower = mb_strtolower($note->content ?? '');
            $tagsStr = is_array($note->tags) ? mb_strtolower(implode(' ', $note->tags)) : '';

            if (str_starts_with($titleLower, $lowercaseQuery)) {
                $textScore = max($textScore, 0.95);
            } elseif (str_contains($titleLower, $lowercaseQuery)) {
                $textScore = max($textScore, 0.85);
            }

            if (str_contains($tagsStr, $lowercaseQuery)) {
                $textScore = max($textScore, 0.90);
            }

            if (str_contains($contentLower, $lowercaseQuery)) {
                $textScore = max($textScore, 0.75);
            }

            // Combine vector score and text score
            $finalScore = max($vectorScore, $textScore);
            $note->similarity_score = round($finalScore, 4);
            return $note;
        });

        // Filter and sort by similarity score descending
        return $results
            ->filter(fn (Note $note) => $note->similarity_score >= $minSimilarity)
            ->sortByDesc('similarity_score')
            ->take($limit)
            ->values();
    }

    /**
     * Compute cosine similarity between two numeric vectors.
     *
     * @param float[] $vecA
     * @param float[] $vecB
     * @return float
     */
    public function cosineSimilarity(array $vecA, array $vecB): float
    {
        $countA = count($vecA);
        $countB = count($vecB);

        if ($countA === 0 || $countB === 0) {
            return 0.0;
        }

        $minLen = min($countA, $countB);
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < $minLen; $i++) {
            $dotProduct += $vecA[$i] * $vecB[$i];
            $normA += $vecA[$i] * $vecA[$i];
            $normB += $vecB[$i] * $vecB[$i];
        }

        if ($normA == 0.0 || $normB == 0.0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
