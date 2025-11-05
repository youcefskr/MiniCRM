<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KnowledgeBase;
use App\Services\EmbeddingService;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    protected static function booted()
    {
        static::created(function ($category) {
            $content = "Category: {$category->name}\n"
                . "Description: {$category->description}";

            $embedder = new EmbeddingService();
            $vector = $embedder->embed($content);
            KnowledgeBase::create([
                'content' => $content,
                'embedding' =>json_encode($vector),
                'metadata' => json_encode([
                    'type' => 'category',
                    'category_id' => $category->id,
                ]),
            ]);
        });

        static::updated(function ($category) {
            $kb = KnowledgeBase::where('metadata->category_id', $category->id)
                ->where('metadata->type', 'category')
                ->first();

            if ($kb) {
                $content = "Category: {$category->name}\n"
                . "Description: {$category->description}";

                $embedder = new EmbeddingService();
                $vector = $embedder->embed($content);
                $kb->update([
                    'content' => $content,
                'embedding' =>json_encode($vector),
                'metadata' => json_encode([
                    'type' => 'category',
                    'category_id' => $category->id,
                ]),
                ]);
            }
        });

        static::deleted(function ($category) {
            KnowledgeBase::where('metadata->category_id', $category->id)
                ->where('metadata->type', 'category')
                ->delete();
        });
    }
}

