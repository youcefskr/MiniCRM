<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KnowledgeBase;
use App\Services\EmbeddingService;

class Information extends Model
{
    use HasFactory;

    protected $table = 'informations'; // Fix table name

    protected $fillable = ['content'];

    protected static function booted()
    {
        static::created(function ($info) {
            $embedder = new EmbeddingService();
            $vector = $embedder->embed($info->content);

            KnowledgeBase::create([
                'content' => $info->content,
                'embedding' => json_encode($vector),
                'metadata' => json_encode([
                    'type' => 'information',
                    'information_id' => $info->id,
                ]),
            ]);
        });

        static::updated(function ($info) {
            $kb = KnowledgeBase::where('metadata->information_id', $info->id)
                ->where('metadata->type', 'information')
                ->first();

            if ($kb) {
                $embedder = new EmbeddingService();
                $vector = $embedder->embed($info->content);

                $kb->update([
                    'content' => $info->content,
                    'embedding' => json_encode($vector),
                    'metadata' => json_encode([
                        'type' => 'information',
                        'information_id' => $info->id,
                    ]),
                ]);
            }
        });

        static::deleted(function ($info) {
            KnowledgeBase::where('metadata->information_id', $info->id)
                ->where('metadata->type', 'information')
                ->delete();
        });
    }
}
