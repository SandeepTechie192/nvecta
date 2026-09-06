<?php

namespace App\Services\Ai;

class FallbackAiProvider implements AiServiceInterface
{
    protected int $dimension = 128;

    /**
     * Generate a normalized feature vector using character n-grams and token hashing.
     *
     * @param string $text
     * @return float[]
     */
    public function generateEmbedding(string $text): array
    {
        $text = strtolower(trim($text));
        if (empty($text)) {
            return array_fill(0, $this->dimension, 0.0);
        }

        $vector = array_fill(0, $this->dimension, 0.0);

        // Tokenize words
        $words = preg_split('/\s+/', preg_replace('/[^\w\s]/u', '', $text));
        foreach ($words as $word) {
            if (strlen($word) < 1) continue;
            
            // Hash word into vector index
            $hash = crc32($word) % $this->dimension;
            if ($hash < 0) $hash += $this->dimension;
            $vector[$hash] += 2.0;

            // Character 1-grams and 2-grams for short query fuzzy matching
            for ($i = 0; $i < strlen($word); $i++) {
                $ch = substr($word, $i, 1);
                $chHash = crc32($ch) % $this->dimension;
                if ($chHash < 0) $chHash += $this->dimension;
                $vector[$chHash] += 0.3;
            }

            // Character 3-grams for semantic matching
            for ($i = 0; $i <= strlen($word) - 3; $i++) {
                $ngram = substr($word, $i, 3);
                $ngramHash = crc32($ngram) % $this->dimension;
                if ($ngramHash < 0) $ngramHash += $this->dimension;
                $vector[$ngramHash] += 0.5;
            }
        }

        // L2 Normalization to unit length
        $norm = 0.0;
        foreach ($vector as $val) {
            $norm += $val * $val;
        }

        $norm = sqrt($norm);
        if ($norm > 0) {
            for ($i = 0; $i < $this->dimension; $i++) {
                $vector[$i] = round($vector[$i] / $norm, 6);
            }
        }

        return $vector;
    }

    /**
     * Generate an extractive summary of note content.
     *
     * @param string $text
     * @return string
     */
    public function generateSummary(string $text): string
    {
        $text = trim($text);
        if (empty($text)) {
            return 'Empty note content provided.';
        }

        $sentences = preg_split('/(?<=[.?!])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        if (count($sentences) <= 2) {
            return '📌 Key Takeaway: ' . implode(' ', $sentences);
        }

        // Pick top sentences (first sentence + most informative sentence)
        $first = trim($sentences[0]);
        $second = trim($sentences[min(1, count($sentences) - 1)]);
        
        return "📌 Summary: {$first} Furthermore, {$second}";
    }
}
