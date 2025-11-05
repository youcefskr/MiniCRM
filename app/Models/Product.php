<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\KnowledgeBase;
use App\Services\EmbeddingService;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'price',
        'description',
        'image',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected static function booted()
    {
        static::created(function ($product) {
            $content = "Product: {$product->name}\n"
                . "Brand: {$product->brand}\n"
                . "Category: {$product->category->name}\n"
                . "Price: {$product->price}\n"
                . "Description: {$product->description}";

            $embedder = new EmbeddingService();
            $vector = $embedder->embed($content);
            KnowledgeBase::create([
                'content' => $content,
                'embedding' =>json_encode($vector),
                'metadata' => json_encode([
                    'type' => 'product',
                    'product_id' => $product->id,
                    'category_id' => $product->category_id,
                ]),
            ]);
        });

        static::updated(function ($product) {
            $kb = KnowledgeBase::where('metadata->product_id', $product->id)
                ->where('metadata->type', 'product')
                ->first();

            if ($kb) {
                $content = "Product: {$product->name}\n"
                    . "Brand: {$product->brand}\n"
                    . "Category: {$product->category->name}\n"
                    . "Price: {$product->price}\n"
                    . "Description: {$product->description}";

                $embedder = new EmbeddingService();
                $vector = $embedder->embed($content);
                $kb->update([
                    'content' => $content,
                    'embedding' => json_encode($vector),
                    'metadata' => json_encode([
                        'type' => 'product',
                        'product_id' => $product->id,
                        'category_id' => $product->category_id,
                    ]),
                ]);
            }
        });

        static::deleted(function ($product) {
            KnowledgeBase::where('metadata->product_id', $product->id)
                ->where('metadata->type', 'product')
                ->delete();
        });
    }
}

