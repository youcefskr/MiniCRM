<?php

namespace App\Observers;

use App\Services\ActivityLogService;
use Illuminate\Database\Eloquent\Model;

class ActivityLogObserver
{
    /**
     * Handle the "created" event.
     */
    public function created(Model $model): void
    {
        ActivityLogService::logCreated($model);
    }

    /**
     * Handle the "updated" event.
     */
    public function updated(Model $model): void
    {
        ActivityLogService::logUpdated($model);
    }

    /**
     * Handle the "deleted" event.
     */
    public function deleted(Model $model): void
    {
        ActivityLogService::logDeleted($model);
    }

    /**
     * Handle the "restored" event (for soft deletes).
     */
    public function restored(Model $model): void
    {
        ActivityLogService::logRestored($model);
    }
}
