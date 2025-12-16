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

    /**
     * Scope a query to filter interactions.
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('contact', function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenom', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('notes', function ($q) use ($search) {
                    $q->where('contenu', 'like', "%{$search}%");
                });
            });
        })->when($filters['type'] ?? null, function ($query, $type) {
            $query->where('type_id', $type);
        })->when($filters['statut'] ?? null, function ($query, $statut) {
            $query->where('statut', $statut);
        })->when($filters['user'] ?? null, function ($query, $user) {
            $query->where('user_id', $user);
        })->when($filters['date_from'] ?? null, function ($query, $date) {
            $query->whereDate('date_interaction', '>=', $date);
        })->when($filters['date_to'] ?? null, function ($query, $date) {
            $query->whereDate('date_interaction', '<=', $date);
        })->when($filters['date'] ?? null, function ($query, $date) {
            switch ($date) {
                case 'today':
                    $query->whereDate('date_interaction', today());
                    break;
                case 'week':
                    $query->whereDate('date_interaction', '>=', now()->startOfWeek());
                    break;
                case 'month':
                    $query->whereMonth('date_interaction', now()->month)
                          ->whereYear('date_interaction', now()->year);
                    break;
                case 'year':
                    $query->whereYear('date_interaction', now()->year);
                    break;
            }
        });
    }
}