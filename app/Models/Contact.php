<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'entreprise',
        'adresse',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accesseur pour le nom complet
    public function getFullNameAttribute()
    {
        return "{$this->prenom} {$this->nom}";
    }

    /**
     * Get the interactions for the contact.
     */
    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }
}