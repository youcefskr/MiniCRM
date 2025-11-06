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
        // On récupère tout pour le filtrage côté client (Alpine.js)
        $tasks = Task::with(['user', 'contact'])->latest()->get();
        $users = User::select('id', 'name')->get();
        $contacts = Contact::select('id', 'nom', 'prenom')->get();
        
        return view('tasks.index', compact('tasks', 'users', 'contacts'));
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