<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'priority',
        'status',
        'user_id',
        'contact_id',
    ];

    /**
     * Une tâche appartient à un utilisateur (assigné).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Une tâche peut être liée à un contact.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}