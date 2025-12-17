<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use App\Notifications\SensitiveActivityNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ActivityLogService
{
    /**
     * Champs à ignorer lors du tracking
     */
    protected static array $ignoredFields = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'email_verified_at',
    ];

    /**
     * Mapping des modèles vers les modules
     */
    protected static array $moduleMapping = [
        'App\Models\Contact' => 'contacts',
        'App\Models\Opportunity' => 'opportunities',
        'App\Models\Product' => 'products',
        'App\Models\Category' => 'categories',
        'App\Models\Interaction' => 'interactions',
        'App\Models\Task' => 'tasks',
        'App\Models\User' => 'users',
        'App\Models\Subscription' => 'subscriptions',
        'App\Models\Invoice' => 'invoices',
        'App\Models\InvoiceItem' => 'invoices',
        'App\Models\Message' => 'messages',
        'App\Models\Conversation' => 'messages',
        'App\Models\TypeInteraction' => 'types_interactions',
    ];

    /**
     * Actions considérées comme sensibles
     */
    protected static array $sensitiveActions = [
        'delete',
        'bulk_delete',
        'role_change',
        'password_change',
    ];

    /**
     * Modules sensibles (toute modification = alerte)
     */
    protected static array $sensitiveModules = [
        'users',
        'roles',
        'permissions',
    ];

    /**
     * Log la création d'un modèle
     */
    public static function logCreated(Model $model): ActivityLog
    {
        $module = self::getModuleFromModel($model);
        $name = self::getModelDisplayName($model);
        
        $log = ActivityLog::log(
            action: 'create',
            module: $module,
            description: "Création de {$name} dans {$module}",
            model: $model,
            newValues: self::getLoggableAttributes($model),
            isSensitive: in_array($module, self::$sensitiveModules),
            severity: in_array($module, self::$sensitiveModules) ? 'warning' : 'info'
        );

        self::notifyIfSensitive($log);
        return $log;
    }

    /**
     * Log la mise à jour d'un modèle
     */
    public static function logUpdated(Model $model): ?ActivityLog
    {
        $module = self::getModuleFromModel($model);
        $name = self::getModelDisplayName($model);
        
        $oldValues = self::filterAttributes($model->getOriginal());
        $newValues = self::filterAttributes($model->getAttributes());
        
        // Ne pas logger si aucun changement significatif
        $changes = array_diff_assoc($newValues, $oldValues);
        if (empty($changes)) {
            return null;
        }

        // Détecter les changements de rôle
        $isSensitive = in_array($module, self::$sensitiveModules);
        $severity = 'info';

        // Vérifier si c'est un changement de mot de passe
        if ($model instanceof User && $model->isDirty('password')) {
            $isSensitive = true;
            $severity = 'warning';
        }

        $log = ActivityLog::log(
            action: 'update',
            module: $module,
            description: "Modification de {$name} dans {$module}",
            model: $model,
            oldValues: $oldValues,
            newValues: $newValues,
            isSensitive: $isSensitive,
            severity: $severity
        );

        self::notifyIfSensitive($log);
        return $log;
    }

    /**
     * Log la suppression d'un modèle
     */
    public static function logDeleted(Model $model): ActivityLog
    {
        $module = self::getModuleFromModel($model);
        $name = self::getModelDisplayName($model);
        
        $log = ActivityLog::log(
            action: 'delete',
            module: $module,
            description: "Suppression de {$name} dans {$module}",
            model: $model,
            oldValues: self::getLoggableAttributes($model),
            isSensitive: true,
            severity: 'danger'
        );

        self::notifyIfSensitive($log);
        return $log;
    }

    /**
     * Log la restauration d'un modèle (soft delete)
     */
    public static function logRestored(Model $model): ActivityLog
    {
        $module = self::getModuleFromModel($model);
        $name = self::getModelDisplayName($model);
        
        return ActivityLog::log(
            action: 'restore',
            module: $module,
            description: "Restauration de {$name} dans {$module}",
            model: $model
        );
    }

    /**
     * Log un changement de statut
     */
    public static function logStatusChange(Model $model, string $oldStatus, string $newStatus): ActivityLog
    {
        $module = self::getModuleFromModel($model);
        $name = self::getModelDisplayName($model);
        
        return ActivityLog::log(
            action: 'status_change',
            module: $module,
            description: "{$name}: statut changé de '{$oldStatus}' à '{$newStatus}'",
            model: $model,
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => $newStatus]
        );
    }

    /**
     * Log un changement de rôle utilisateur
     */
    public static function logRoleChange(User $user, array $oldRoles, array $newRoles): ActivityLog
    {
        $log = ActivityLog::log(
            action: 'role_change',
            module: 'users',
            description: "Changement de rôles pour {$user->name}",
            model: $user,
            oldValues: ['roles' => $oldRoles],
            newValues: ['roles' => $newRoles],
            isSensitive: true,
            severity: 'danger'
        );

        self::notifyIfSensitive($log);
        return $log;
    }

    /**
     * Log une suppression massive
     */
    public static function logBulkDelete(string $module, int $count, array $ids): ActivityLog
    {
        $log = ActivityLog::log(
            action: 'bulk_delete',
            module: $module,
            description: "Suppression massive de {$count} éléments dans {$module}",
            isSensitive: true,
            severity: 'danger'
        );

        // Ajouter les IDs supprimés
        $log->update(['old_values' => ['deleted_ids' => $ids]]);

        self::notifyIfSensitive($log);
        return $log;
    }

    /**
     * Log d'export
     */
    public static function logExport(string $module, int $count): ActivityLog
    {
        return ActivityLog::log(
            action: 'export',
            module: $module,
            description: "Export de {$count} éléments depuis {$module}"
        );
    }

    /**
     * Obtenir le module depuis un modèle
     */
    protected static function getModuleFromModel(Model $model): string
    {
        $class = get_class($model);
        return self::$moduleMapping[$class] ?? strtolower(class_basename($model)) . 's';
    }

    /**
     * Obtenir un nom d'affichage pour le modèle
     */
    protected static function getModelDisplayName(Model $model): string
    {
        // Essayer différents attributs
        if (isset($model->name)) return $model->name;
        if (isset($model->title)) return $model->title;
        if (isset($model->nom) && isset($model->prenom)) return "{$model->nom} {$model->prenom}";
        if (isset($model->nom)) return $model->nom;
        if (isset($model->invoice_number)) return $model->invoice_number;
        if (isset($model->email)) return $model->email;
        if (isset($model->subject)) return $model->subject;

        return class_basename($model) . ' #' . $model->id;
    }

    /**
     * Filtrer les attributs pour ne garder que les loggables
     */
    protected static function filterAttributes(array $attributes): array
    {
        return array_filter($attributes, function ($key) {
            return !in_array($key, self::$ignoredFields);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Obtenir les attributs loggables d'un modèle
     */
    protected static function getLoggableAttributes(Model $model): array
    {
        return self::filterAttributes($model->getAttributes());
    }

    /**
     * Notifier les admins si l'action est sensible
     */
    protected static function notifyIfSensitive(ActivityLog $log): void
    {
        if (!$log->is_sensitive) {
            return;
        }

        // Récupérer tous les admins
        $admins = User::role('admin')->get();
        
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new SensitiveActivityNotification($log));
        }
    }
}
