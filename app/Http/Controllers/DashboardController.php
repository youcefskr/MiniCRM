<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Opportunity;
use App\Models\Task;
use App\Models\Interaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'contacts_count' => Contact::count(),
            'opportunities_value' => Opportunity::sum('value'),
            'opportunities_count' => Opportunity::count(),
            'tasks_pending' => Task::where('status', 'en attente')->count(),
            'interactions_today' => Interaction::whereDate('date_interaction', today())->count(),
        ];

        $recentOpportunities = Opportunity::with('contact')
            ->latest()
            ->take(5)
            ->get();

        $recentTasks = Task::with('contact')
            ->where('status', '!=', 'terminee')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        $recentInteractions = Interaction::with(['contact', 'type'])
            ->latest('date_interaction')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentOpportunities', 'recentTasks', 'recentInteractions'));
    }
}
