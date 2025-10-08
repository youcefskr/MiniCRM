<?php

namespace Database\Seeders;

use App\Models\TypeInteraction;
use Illuminate\Database\Seeder;

class TypesInteractionsSeeder extends Seeder
{
    public function run()
    {
        $types = [
            [
                'nom' => 'Appel',
                'description' => 'Interaction téléphonique',
            ],
            [
                'nom' => 'E-mail',
                'description' => 'Communication par courrier électronique',
            ],
            [
                'nom' => 'Réunion',
                'description' => 'Rencontre physique ou virtuelle',
            ],
        ];

        foreach ($types as $type) {
            TypeInteraction::create($type);
        }
    }
}