<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeInteraction extends Model
{
    protected $table = 'types_interactions';
    
    protected $fillable = [
        'nom',
        'description',
        'couleur'
    ];

    /**
     * Get the interactions for this type.
     */
    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class, 'type_id');
    }

    /**
     * Get CSS classes for badge color
     */
    public function getBadgeClasses(): string
    {
        $couleur = $this->couleur ?? 'gray';
        $classes = [
            'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            'green' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'purple' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
            'yellow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'orange' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
            'red' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            'gray' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
            'indigo' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
            'pink' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300',
        ];

        return $classes[$couleur] ?? $classes['gray'];
    }

    /**
     * Get CSS classes for icon background color
     */
    public function getIconBgClasses(): string
    {
        $couleur = $this->couleur ?? 'gray';
        $classes = [
            'blue' => 'bg-blue-500',
            'green' => 'bg-green-500',
            'purple' => 'bg-purple-500',
            'yellow' => 'bg-yellow-500',
            'orange' => 'bg-orange-500',
            'red' => 'bg-red-500',
            'gray' => 'bg-gray-500',
            'indigo' => 'bg-indigo-500',
            'pink' => 'bg-pink-500',
        ];

        return $classes[$couleur] ?? $classes['gray'];
    }
}