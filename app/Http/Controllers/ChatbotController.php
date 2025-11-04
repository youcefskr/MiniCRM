<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiService;
use App\Models\MessageAi;
use App\Models\KnowledgeBase;


class ChatbotController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function respond(Request $request)
    {
        $clientMessage = $request->input('message');

        // Step 1: Retrieve relevant info (RAG layer)
        $context = $this->getRelevantContext($clientMessage);

        // Step 2: Build the prompt
        $prompt = "Context: $context\nClient message: $clientMessage\nProvide a helpful, polite response:";

        // Step 3: Get AI response
        $aiResponse = $this->gemini->generateResponse($prompt);

        // Step 4: Save both messages in DB
        MessageAi::create([
            'sender' => 'client',
            'content' => $clientMessage,
        ]);
        MessageAi::create([
            'sender' => 'ai',
            'content' => $aiResponse,
        ]);

        return response()->json(['response' => $aiResponse]);
    }

    protected function getRelevantContext($message)
    {
        $embedder = new \App\Services\EmbeddingService();
        $queryVector = $embedder->embed($message);

        $docs = KnowledgeBase::all();

        $results = $docs->map(function ($doc) use ($queryVector) {
            $docVector = json_decode($doc->embedding, true);
            $similarity = $this->cosineSimilarity($queryVector, $docVector);
            return ['content' => $doc->content, 'score' => $similarity];
        })->sortByDesc('score')->take(3);

        return $results->pluck('content')->implode("\n");
    }

    protected function cosineSimilarity($a, $b)
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        for ($i = 0; $i < count($a); $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] ** 2;
            $normB += $b[$i] ** 2;
        }
        if ($normA == 0 || $normB == 0) return 0;
        return $dot / (sqrt($normA) * sqrt($normB));
    }
}
