<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiProvider implements AiServiceInterface
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $embeddingModel;
    protected string $summaryModel;
    protected FallbackAiProvider $fallback;

    public function __construct(string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? env('OPENAI_API_KEY', '');
        
        // Auto-detect OpenRouter URL if key starts with sk-or-v1-
        $defaultUrl = str_starts_with($this->apiKey, 'sk-or-v1-') 
            ? 'https://openrouter.ai/api/v1' 
            : 'https://api.openai.com/v1';

        $this->baseUrl = env('OPENAI_BASE_URL', $defaultUrl);
        $this->embeddingModel = env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small');
        
        $defaultSummaryModel = str_contains($this->baseUrl, 'openrouter')
            ? 'openrouter/free'
            : 'gpt-4o-mini';

        $this->summaryModel = env('OPENAI_SUMMARY_MODEL', $defaultSummaryModel);
        $this->fallback = new FallbackAiProvider();
    }

    protected function getHeaders(): array
    {
        $headers = [];
        if (str_contains($this->baseUrl, 'openrouter')) {
            $headers['HTTP-Referer'] = 'http://localhost:8000';
            $headers['X-Title'] = 'Smart Note AI';
        }
        return $headers;
    }

    public function generateEmbedding(string $text): array
    {
        if (empty($this->apiKey)) {
            return $this->fallback->generateEmbedding($text);
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->withHeaders($this->getHeaders())
                ->timeout(10)
                ->post(rtrim($this->baseUrl, '/') . '/embeddings', [
                    'model' => $this->embeddingModel,
                    'input' => $text,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['data'][0]['embedding'])) {
                    return $data['data'][0]['embedding'];
                }
            }

            Log::warning('AI Embedding API error, using fallback: ' . $response->body());
        } catch (\Throwable $e) {
            Log::error('AI Embedding Exception: ' . $e->getMessage());
        }

        return $this->fallback->generateEmbedding($text);
    }

    public function generateSummary(string $text): string
    {
        if (empty($this->apiKey)) {
            return $this->fallback->generateSummary($text);
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->withHeaders($this->getHeaders())
                ->timeout(15)
                ->post(rtrim($this->baseUrl, '/') . '/chat/completions', [
                    'model' => $this->summaryModel,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an expert AI notes assistant. Create a concise, clear 2-3 sentence summary of the given note text. Highlight key insights, action items, or core takeaways.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $text,
                        ],
                    ],
                    'max_tokens' => 150,
                    'temperature' => 0.5,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? null;
                if (!empty($content)) {
                    return trim($content);
                }
            }

            Log::warning('AI Summary API error (' . $response->status() . '), using fallback: ' . $response->body());
        } catch (\Throwable $e) {
            Log::error('AI Summary Exception: ' . $e->getMessage());
        }

        return $this->fallback->generateSummary($text);
    }
}
