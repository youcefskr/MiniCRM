<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteInteraction extends Model
{
    use HasFactory;

    protected $table = 'notes_interactions';

    protected $fillable = [
        'interaction_id',
        'user_id',
        'contenu',
    ];

    public function interaction()
    {
        return $this->belongsTo(Interaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}