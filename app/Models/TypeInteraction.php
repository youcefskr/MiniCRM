<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeInteraction extends Model
{
    protected $table = 'types_interactions';
    
    protected $fillable = [
        'nom',
        'description'
    ];

    // Ajoutez cette propriété pour définir explicitement la clé de route
    public function getRouteKeyName()
    {
        return 'id';
    }
}