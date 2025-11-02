<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Interaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'type_id',
        'user_id',
        'description',
        'date_interaction',
        'statut'
    ];

    protected $casts = [
        'date_interaction' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the type of this interaction.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(TypeInteraction::class, 'type_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(NoteInteraction::class);
    }
}