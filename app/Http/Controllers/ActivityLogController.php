<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Afficher le journal d'activité
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('user_id')) {
            $query->forUser($request->user_id);
        }

        if ($request->filled('action')) {
            $query->forAction($request->action);
        }

        if ($request->filled('module')) {
            $query->forModule($request->module);
        }

        if ($request->filled('severity')) {
            if ($request->severity === 'sensitive') {
                $query->sensitive();
            } else {
                $query->where('severity', $request->severity);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('model_name', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(30)->withQueryString();

        // Statistiques
        $stats = [
            'total_today' => ActivityLog::today()->count(),
            'total_week' => ActivityLog::thisWeek()->count(),
            'total_month' => ActivityLog::thisMonth()->count(),
            'sensitive_count' => ActivityLog::sensitive()->thisMonth()->count(),
            'creates' => ActivityLog::forAction('create')->thisMonth()->count(),
            'updates' => ActivityLog::forAction('update')->thisMonth()->count(),
            'deletes' => ActivityLog::forAction('delete')->thisMonth()->count(),
            'logins' => ActivityLog::forAction('login')->thisMonth()->count(),
        ];

        // Activité par module (ce mois)
        $moduleStats = ActivityLog::thisMonth()
            ->selectRaw('module, COUNT(*) as count')
            ->groupBy('module')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('count', 'module')
            ->toArray();

        // Dernières alertes sensibles
        $recentAlerts = ActivityLog::sensitive()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Utilisateurs les plus actifs
        $activeUsers = ActivityLog::thisMonth()
            ->whereNotNull('user_id')
            ->selectRaw('user_id, user_name, COUNT(*) as actions_count')
            ->groupBy('user_id', 'user_name')
            ->orderByDesc('actions_count')
            ->limit(5)
            ->get();

        // Données pour les filtres
        $users = User::orderBy('name')->get();
        $actions = ActivityLog::distinct()->pluck('action')->sort()->values();
        $modules = ActivityLog::distinct()->pluck('module')->sort()->values();

        return view('admin.activity-logs.index', compact(
            'logs', 'stats', 'moduleStats', 'recentAlerts', 
            'activeUsers', 'users', 'actions', 'modules'
        ));
    }

    /**
     * Afficher le détail d'un log
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');

        // Logs liés au même élément
        $relatedLogs = [];
        if ($activityLog->model_type && $activityLog->model_id) {
            $relatedLogs = ActivityLog::forModel($activityLog->model_type, $activityLog->model_id)
                ->where('id', '!=', $activityLog->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        return view('admin.activity-logs.show', compact('activityLog', 'relatedLogs'));
    }

    /**
     * Afficher l'historique d'un élément spécifique
     */
    public function modelHistory(Request $request)
    {
        $modelType = $request->model_type;
        $modelId = $request->model_id;

        if (!$modelType || !$modelId) {
            return back()->with('error', 'Modèle non spécifié');
        }

        $logs = ActivityLog::forModel($modelType, $modelId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.activity-logs.model-history', compact('logs', 'modelType', 'modelId'));
    }

    /**
     * Afficher l'historique d'un utilisateur
     */
    public function userHistory(User $user)
    {
        $logs = ActivityLog::forUser($user->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $stats = [
            'total' => ActivityLog::forUser($user->id)->count(),
            'this_month' => ActivityLog::forUser($user->id)->thisMonth()->count(),
            'creates' => ActivityLog::forUser($user->id)->forAction('create')->count(),
            'updates' => ActivityLog::forUser($user->id)->forAction('update')->count(),
            'deletes' => ActivityLog::forUser($user->id)->forAction('delete')->count(),
        ];

        return view('admin.activity-logs.user-history', compact('logs', 'user', 'stats'));
    }

    /**
     * Export des logs
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Appliquer les mêmes filtres que l'index
        if ($request->filled('user_id')) {
            $query->forUser($request->user_id);
        }
        if ($request->filled('action')) {
            $query->forAction($request->action);
        }
        if ($request->filled('module')) {
            $query->forModule($request->module);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->limit(5000)->get();

        $filename = 'journal_activite_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Date/Heure', 'Utilisateur', 'Action', 'Module', 
                'Élément', 'Description', 'IP', 'Sévérité'
            ], ';');

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('d/m/Y H:i:s'),
                    $log->user_name,
                    $log->action_label,
                    $log->module_label,
                    $log->model_name ?? '-',
                    $log->description,
                    $log->ip_address,
                    $log->severity,
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Tableau de bord sécurité
     */
    public function securityDashboard()
    {
        // Connexions suspectes (plusieurs échecs)
        $failedLogins = ActivityLog::forAction('login_failed')
            ->thisMonth()
            ->selectRaw('ip_address, COUNT(*) as attempts')
            ->groupBy('ip_address')
            ->having('attempts', '>=', 3)
            ->orderByDesc('attempts')
            ->get();

        // Suppressions récentes
        $recentDeletions = ActivityLog::forAction('delete')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Modifications de rôles
        $roleChanges = ActivityLog::forAction('role_change')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Alertes sensibles non résolues
        $sensitiveAlerts = ActivityLog::sensitive()
            ->with('user')
            ->where('severity', 'danger')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.activity-logs.security', compact(
            'failedLogins', 'recentDeletions', 'roleChanges', 'sensitiveAlerts'
        ));
    }
}
