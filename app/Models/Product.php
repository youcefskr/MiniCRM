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
        'code',
        'brand',
        'price',
        'stock_quantity',
        'is_active',
        'type',
        'description',
        'image',
        'category_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function opportunities()
    {
        return $this->belongsToMany(Opportunity::class, 'opportunity_product')
            ->withPivot(['quantity', 'unit_price', 'discount', 'total_price'])
            ->withTimestamps();
    }

    /**
     * Get the subscriptions that include this product.
     */
    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'subscription_product')
            ->withPivot(['quantity', 'unit_price', 'discount'])
            ->withTimestamps();
    }

    /**
     * Get the invoice items for this product.
     */
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        })->when($filters['category'] ?? null, function ($query, $category) {
            $query->where('category_id', $category);
        })->when($filters['type'] ?? null, function ($query, $type) {
            $query->where('type', $type);
        })->when($filters['stock'] ?? null, function ($query, $stock) {
            if ($stock === 'in_stock') {
                $query->where('stock_quantity', '>', 0);
            } elseif ($stock === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            }
        });
    }

    // Accesseurs
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2, ',', ' ') . ' €';
    }

    public function getStockStatusAttribute()
    {
        if ($this->type === 'service') {
            return 'N/A';
        }
        
        if ($this->stock_quantity <= 0) {
            return 'Rupture';
        } elseif ($this->stock_quantity < 10) {
            return 'Stock faible';
        }
        return 'En stock';
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

