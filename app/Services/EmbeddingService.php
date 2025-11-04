<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmbeddingService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/text-embedding-004:embedContent';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function embed($text)
    {
        $response = Http::post($this->baseUrl . '?key=' . $this->apiKey, [
            'content' => ['parts' => [['text' => $text]]]
        ]);

        if ($response->successful()) {
            return $response->json()['embedding']['values'] ?? [];
        }

        return [];
    }
}
