<?php

namespace App\Services;

use App\Services\Ai\AiServiceInterface;
use App\Services\Ai\OpenAiProvider;
use App\Services\Ai\FallbackAiProvider;

class AiService implements AiServiceInterface
{
    protected AiServiceInterface $provider;

    public function __construct()
    {
        $apiKey = env('OPENAI_API_KEY');
        if (!empty($apiKey)) {
            $this->provider = new OpenAiProvider($apiKey);
        } else {
            $this->provider = new FallbackAiProvider();
        }
    }

    public function generateEmbedding(string $text): array
    {
        return $this->provider->generateEmbedding($text);
    }

    public function generateSummary(string $text): string
    {
        return $this->provider->generateSummary($text);
    }
}
