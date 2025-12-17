<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    /**
     * Les logs ne peuvent pas être modifiés (lecture seule)
     */
    protected $guarded = ['id'];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
        'is_sensitive' => 'boolean',
    ];

    // ========== RELATIONS ==========
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ========== SCOPES ==========

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeForModel($query, $modelType, $modelId = null)
    {
        $query->where('model_type', $modelType);
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        return $query;
    }

    public function scopeSensitive($query)
    {
        return $query->where('is_sensitive', true);
    }

    public function scopeInPeriod($query, $startDate, $endDate = null)
    {
        $query->whereDate('created_at', '>=', $startDate);
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        return $query;
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now()->toDateString());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    // ========== ACCESSORS ==========

    public function getActionLabelAttribute(): string
    {
        $labels = [
            'create' => 'Création',
            'update' => 'Modification',
            'delete' => 'Suppression',
            'restore' => 'Restauration',
            'login' => 'Connexion',
            'logout' => 'Déconnexion',
            'login_failed' => 'Échec de connexion',
            'view' => 'Consultation',
            'export' => 'Export',
            'import' => 'Import',
            'send' => 'Envoi',
            'payment' => 'Paiement',
            'status_change' => 'Changement de statut',
            'role_change' => 'Changement de rôle',
            'password_change' => 'Changement de mot de passe',
            'bulk_delete' => 'Suppression massive',
        ];

        return $labels[$this->action] ?? ucfirst($this->action);
    }

    public function getModuleLabelAttribute(): string
    {
        $labels = [
            'contacts' => 'Contacts',
            'opportunities' => 'Opportunités',
            'products' => 'Produits',
            'categories' => 'Catégories',
            'interactions' => 'Interactions',
            'tasks' => 'Tâches',
            'users' => 'Utilisateurs',
            'roles' => 'Rôles',
            'permissions' => 'Permissions',
            'subscriptions' => 'Abonnements',
            'invoices' => 'Factures',
            'messages' => 'Messages',
            'auth' => 'Authentification',
            'system' => 'Système',
        ];

        return $labels[$this->module] ?? ucfirst($this->module);
    }

    public function getActionIconAttribute(): string
    {
        $icons = [
            'create' => 'plus-circle',
            'update' => 'pencil-square',
            'delete' => 'trash',
            'restore' => 'arrow-uturn-left',
            'login' => 'arrow-right-on-rectangle',
            'logout' => 'arrow-left-on-rectangle',
            'login_failed' => 'exclamation-triangle',
            'view' => 'eye',
            'export' => 'arrow-down-tray',
            'import' => 'arrow-up-tray',
            'send' => 'paper-airplane',
            'payment' => 'banknotes',
            'status_change' => 'arrow-path',
            'role_change' => 'shield-check',
            'password_change' => 'key',
            'bulk_delete' => 'trash',
        ];

        return $icons[$this->action] ?? 'document';
    }

    public function getActionColorAttribute(): string
    {
        $colors = [
            'create' => 'green',
            'update' => 'blue',
            'delete' => 'red',
            'restore' => 'yellow',
            'login' => 'green',
            'logout' => 'zinc',
            'login_failed' => 'red',
            'view' => 'zinc',
            'export' => 'purple',
            'import' => 'purple',
            'send' => 'blue',
            'payment' => 'green',
            'status_change' => 'orange',
            'role_change' => 'red',
            'password_change' => 'orange',
            'bulk_delete' => 'red',
        ];

        return $colors[$this->action] ?? 'zinc';
    }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'danger' => 'red',
            'warning' => 'orange',
            default => 'zinc',
        };
    }

    public function getChangedFieldsListAttribute(): string
    {
        if (empty($this->changed_fields)) {
            return '-';
        }
        return implode(', ', $this->changed_fields);
    }

    // ========== STATIC METHODS ==========

    /**
     * Enregistrer une activité
     */
    public static function log(
        string $action,
        string $module,
        string $description,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        bool $isSensitive = false,
        string $severity = 'info'
    ): self {
        $user = Auth::user();
        $request = request();

        // Déterminer les champs modifiés
        $changedFields = null;
        if ($oldValues && $newValues) {
            $changedFields = array_keys(array_diff_assoc($newValues, $oldValues));
        }

        return self::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'Système',
            'action' => $action,
            'module' => $module,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'model_name' => $model ? self::getModelName($model) : null,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changed_fields' => $changedFields,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'url' => $request?->fullUrl(),
            'method' => $request?->method(),
            'is_sensitive' => $isSensitive,
            'severity' => $severity,
        ]);
    }

    /**
     * Obtenir un nom lisible pour le modèle
     */
    protected static function getModelName(Model $model): ?string
    {
        // Essayer différents attributs communs
        $nameAttributes = ['name', 'title', 'nom', 'invoice_number', 'email', 'subject'];
        
        foreach ($nameAttributes as $attr) {
            if (isset($model->$attr)) {
                return $model->$attr;
            }
        }

        // Pour les contacts, combiner nom et prénom
        if (isset($model->nom) && isset($model->prenom)) {
            return $model->nom . ' ' . $model->prenom;
        }

        return null;
    }

    /**
     * Log de connexion
     */
    public static function logLogin(User $user): self
    {
        return self::log(
            action: 'login',
            module: 'auth',
            description: "Connexion de l'utilisateur {$user->name}",
            model: $user
        );
    }

    /**
     * Log de déconnexion
     */
    public static function logLogout(User $user): self
    {
        return self::log(
            action: 'logout',
            module: 'auth',
            description: "Déconnexion de l'utilisateur {$user->name}",
            model: $user
        );
    }

    /**
     * Log d'échec de connexion
     */
    public static function logLoginFailed(string $email): self
    {
        return self::log(
            action: 'login_failed',
            module: 'auth',
            description: "Tentative de connexion échouée pour: {$email}",
            isSensitive: true,
            severity: 'warning'
        );
    }

    /**
     * Empêcher la modification des logs
     */
    public static function boot()
    {
        parent::boot();

        // Empêcher la mise à jour
        static::updating(function ($model) {
            return false;
        });

        // Empêcher la suppression
        static::deleting(function ($model) {
            return false;
        });
    }
}
