<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Opportunity;
use App\Notifications\OpportunityDueNotification;

class CheckOpportunityDueDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'opportunities:check-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie les opportunités arrivant à échéance (aujourd\'hui et demain) et notifie les utilisateurs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 0;

        // 1. Opportunités pour DEMAIN
        $opportunitiesTomorrow = Opportunity::whereDate('expected_close_date', now()->addDay())
                     ->where('status', '!=', 'gagne')
                     ->where('status', '!=', 'perdu')
                     ->whereNotNull('user_id')
                     ->with('user')
                     ->get();

        foreach ($opportunitiesTomorrow as $opportunity) {
            if ($opportunity->user) {
                $opportunity->user->notify(new OpportunityDueNotification($opportunity, 'tomorrow'));
                $count++;
            }
        }

        // 2. Opportunités pour AUJOURD'HUI
        $opportunitiesToday = Opportunity::whereDate('expected_close_date', now())
                     ->where('status', '!=', 'gagne')
                     ->where('status', '!=', 'perdu')
                     ->whereNotNull('user_id')
                     ->with('user')
                     ->get();

        foreach ($opportunitiesToday as $opportunity) {
            if ($opportunity->user) {
                $opportunity->user->notify(new OpportunityDueNotification($opportunity, 'today'));
                $count++;
            }
        }

        $this->info("{$count} notifications d'opportunités envoyées (Aujourd'hui + Demain).");
    }
}
