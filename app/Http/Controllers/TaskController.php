<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Affiche la vue principale (Kanban).
     */
    public function index()
    {
        $tasks = Task::with(['user', 'contact'])->latest()->get();
        $users = User::select('id', 'name')->get();
        $contacts = Contact::select('id', 'nom', 'prenom')->get();
        
        $groupedTasks = $tasks->groupBy('status');
        $statuses = [
            'en attente' => 'En attente',
            'en cours' => 'En cours',
            'terminee' => 'Terminée'
        ];

        $stats = [
            'total' => $tasks->count(),
            'par_statut' => $tasks->groupBy('status')->map(function ($group, $status) {
                return (object) [
                    'statut' => $status,
                    'count' => $group->count()
                ];
            })
        ];
        
        return view('tasks.index', compact('tasks', 'groupedTasks', 'statuses', 'users', 'contacts', 'stats'));
    }

    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:en attente,en cours,terminee',
        ]);

        $task->update(['status' => $validated['status']]);

        return response()->json(['success' => true, 'message' => 'Statut mis à jour.']);
    }

    /**
     * Enregistre une nouvelle tâche.
     */
    public function store(Request $request)
    {
        // Supprimer ou commenter cette ligne
        // dd($request->all());
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:basse,normale,haute',
            'status' => 'required|in:en attente,en cours,terminee',
            'user_id' => 'required|exists:users,id',
            'contact_id' => 'nullable|exists:contacts,id',
        ]);

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Tâche créée avec succès.');
    }

    /**
     * Met à jour une tâche existante.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:basse,normale,haute',
            'status' => 'required|in:en attente,en cours,terminee',
            'user_id' => 'required|exists:users,id',
            'contact_id' => 'nullable|exists:contacts,id',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Tâche mise à jour avec succès.');
    }

    /**
     * Supprime une tâche.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Tâche supprimée avec succès.');
    }
}