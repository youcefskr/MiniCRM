<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Opportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'contact_id',
        'user_id',
        'value',
        'stage',
        'probability',
        'status',
        'notes',
        'expected_close_date',
    ];

    protected $casts = [
        'expected_close_date' => 'date',
        'value' => 'decimal:2',
        'probability' => 'integer',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'opportunity_product')
            ->withPivot(['quantity', 'unit_price', 'discount', 'total_price'])
            ->withTimestamps();
    }
}
