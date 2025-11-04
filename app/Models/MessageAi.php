<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageAi extends Model
{
     protected $fillable = [
        'sender',
        'content',
        'client_id',
        'conversation_id'
    ];
}
