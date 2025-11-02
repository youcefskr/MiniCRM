<?php

namespace Database\Seeders;

use App\Models\TypeInteraction;
use Illuminate\Database\Seeder;

class TypesInteractionsSeeder extends Seeder
{
    public function run()
    {
        // Mapping des anciens noms vers les nouveaux noms
        $migrationMap = [
            'Appel' => 'Appel téléphonique',
            'E-mail' => 'E-mail', // Déjà correct
            'Réunion' => 'Réunion', // Déjà correct
        ];

        // Migrer les anciens types vers les nouveaux noms
        foreach ($migrationMap as $ancienNom => $nouveauNom) {
            TypeInteraction::where('nom', $ancienNom)->update(['nom' => $nouveauNom]);
        }

        $types = [
            [
                'nom' => 'Appel téléphonique',
                'description' => 'Contact direct avec le client par téléphone',
                'couleur' => 'blue',
            ],
            [
                'nom' => 'E-mail',
                'description' => 'Communication écrite et traçable par courrier électronique',
                'couleur' => 'green',
            ],
            [
                'nom' => 'Réunion',
                'description' => 'Rencontre physique ou en ligne pour discuter d\'une opportunité',
                'couleur' => 'purple',
            ],
            [
                'nom' => 'Message instantané',
                'description' => 'Échange rapide via chat interne ou messagerie instantanée',
                'couleur' => 'yellow',
            ],
            [
                'nom' => 'Rappel / Suivi',
                'description' => 'Action planifiée pour relancer ou vérifier une opportunité',
                'couleur' => 'orange',
            ],
            [
                'nom' => 'Note interne',
                'description' => 'Information ou observation enregistrée à usage interne',
                'couleur' => 'gray',
            ],
        ];

        // Utiliser updateOrCreate pour mettre à jour les types existants ou créer les nouveaux
        foreach ($types as $type) {
            TypeInteraction::updateOrCreate(
                ['nom' => $type['nom']], // Recherche par nom
                $type // Données à mettre à jour ou créer
            );
        }
    }
}