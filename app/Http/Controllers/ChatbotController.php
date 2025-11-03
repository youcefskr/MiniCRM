<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiService;
use App\Models\Message;

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
        Message::create([
            'sender' => 'client',
            'content' => $clientMessage,
        ]);
        Message::create([
            'sender' => 'ai',
            'content' => $aiResponse,
        ]);

        return response()->json(['response' => $aiResponse]);
    }

    protected function getRelevantContext($message)
    {
        // Example of RAG: fetch last messages or FAQ
        $previous = Message::latest()->take(5)->pluck('content')->implode("\n");
        return $previous ?: "No previous context available.";
    }
}
