[33mcommit db55ee56099a30a65125a7340609023891aefa9b[m[33m ([m[1;36mHEAD[m[33m -> [m[1;32mmain[m[33m)[m
Author: youcefsaker <youcefsaker201@gamil.com>
Date:   Sat Oct 4 10:07:36 2025 +0100

    Modification de la vue login (Livewire Auth)

[33mcommit 42832e2e0fb760c217e3fed1e7f4324aec05b779[m
Author: youcefsaker <youcefsaker201@gamil.com>
Date:   Sat Oct 4 09:28:49 2025 +0100

     Modification du modèle User
    - Ajout du contrôleur UserController
    - Mise à jour des layouts : header et sidebar
    - Ajout des vues utilisateur (layouts et users)
    - Mise à jour du fichier de routes web.php
    - Ajout des tests pour la gestion des rôles et utilisateurs

[33mcommit a84c3af05c7a362156d49e4afe4c56f8f80e8389[m
Author: youcefsaker <youcefsaker201@gamil.com>
Date:   Fri Oct 3 15:45:02 2025 +0100

    Mise à jour de l'interface et structure des rôles/permissions
    
    - Suppression de l'ancienne vue index des rôles et permissions
    - Ajout de nouvelles vues sous   /views/roles
    - Amélioration de l'en-tête, barre latérale et scripts JS
    - Ajout du composant flash-messages pour les notifications
    - Mise à jour du contrôleur RolePermissionController
    - Ajout du seeder DatabaseSeeder
    - Mise à jour des routes web pour gérer les rôles et permissions

[33mcommit f37219a831a640bb5d8274a25ed69ace7052feed[m
Author: youcefsaker <youcefsaker201@gamil.com>
Date:   Fri Oct 3 01:46:18 2025 +0100

    Configuration des rôles et permissions dans Laravel 12
    
    - Installation du package spatie/laravel-permission
    - Création des seeders : AdminUserSeeder, RolesAndPermissionsSeeder
    - Création du contrôleur RolePermissionController pour la gestion des rôles et permissions
    - Implémentation des vues de gestion (CRUD) des rôles et permissions
    - Attribution du rôle 'admin' à l'utilisateur par défaut dans AdminUserSeeder

[33mcommit 79a23032baa73ce6322dbc778ae1a5d38a125d30[m
Author: youcefsaker <youcefsaker201@gamil.com>
Date:   Fri Oct 3 00:43:33 2025 +0100

    feat: Configuration de Laravel 12 avec Livewire, Tailwind CSS et vérification d'email
    
    - Installation de Laravel Breeze avec la pile Livewire
    - Configuration de Tailwind CSS avec PostCSS
    - Implémentation de la vérification d'adresse email (interface MustVerifyEmail)
    - Mise en place des routes protégées avec le middleware 'verified'
    - Configuration des paramètres SMTP pour l'envoi d'emails
    - Ajout de modèles personnalisés pour la vérification d'email
