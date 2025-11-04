<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function generateResponse($prompt)
    {
        $response = Http::withOptions([
                'verify' => 'C:\xampp\php\extras\ssl\cacert.pem', // Path to CA certificate
            ])->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=AIzaSyBznNRnCzNTeMi3KOtfZu52GHH342TK5NY", [
                "contents" => [
                    ["parts" => [["text" => $prompt]]]
                ]
            ]);

        if ($response->successful()) {
            return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'No response generated.';
        }

         if ($response->failed()) {
                return response()->json([
                    'error' => 'Gemini API call failed',
                    'details' => $response->json(),
                    'status' => $response->status(),
                ], $response->status());
            }

        return 'Error generating response: ' . $response->body();
    }
}
