# Configuration Laravel Reverb pour la Messagerie

## Installation

1. **Installer les dépendances** (déjà fait)
```bash
composer require laravel/reverb
npm install laravel-echo pusher-js
```

2. **Configurer le fichier .env**

Ajoutez ces variables dans votre fichier `.env` :

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Variables pour Vite (frontend)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

4. **Démarrer le serveur Reverb**

Dans un terminal séparé, démarrez le serveur Reverb :
```bash
php artisan reverb:start
```

Ou en production avec Supervisor/PM2.

## Utilisation

Une fois Reverb démarré, la messagerie utilisera automatiquement WebSocket pour les messages en temps réel.

Le système basculera automatiquement vers un polling (toutes les 3 secondes) si Reverb n'est pas disponible.

## Vérification

Pour vérifier que tout fonctionne :
1. Ouvrez la messagerie dans deux navigateurs différents (ou deux onglets en navigation privée)
2. Connectez-vous avec deux comptes différents
3. Créez une conversation entre les deux utilisateurs
4. Envoyez un message depuis un compte
5. Le message devrait apparaître instantanément dans l'autre navigateur sans rafraîchissement

