<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KnowledgeBase;
use App\Services\EmbeddingService;

class KnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        $embedder = new EmbeddingService();

        $texts = [
            "Our office hours are from 9am to 5pm, Monday to Friday.",
            "We offer 24/7 support for premium clients.",
            "You can reset your password by clicking 'Forgot Password' on the login page.",
            "For billing inquiries, please contact the accounting department.",
            "Our headquarters are located in Algiers."
        ];

        foreach ($texts as $text) {
            $vector = $embedder->embed($text);

            KnowledgeBase::create([
                'content' => $text,
                'embedding' => json_encode($vector)
            ]);
        }
    }
}
