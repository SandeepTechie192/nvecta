<?php

namespace App\Services\Ai;

interface AiServiceInterface
{
    /**
     * Generate vector embedding for a given text content.
     *
     * @param string $text
     * @return float[]
     */
    public function generateEmbedding(string $text): array;

    /**
     * Generate an AI-powered concise summary of the provided text.
     *
     * @param string $text
     * @return string
     */
    public function generateSummary(string $text): string;
}
