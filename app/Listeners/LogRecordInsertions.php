<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;

class LogRecordInsertions
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(QueryExecuted $event)
    {
        // Check if the executed query is an insert query
        if (str_starts_with($event->sql, 'insert')) {
            $recordCount = count($event->bindings);
            Log::info("Inserted {$recordCount} record(s) into the database.");
        }
    }
}
