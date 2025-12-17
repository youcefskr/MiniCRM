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
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the subscriptions for the contact.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the invoices for the contact.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the opportunities for the contact.
     */
    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }
}