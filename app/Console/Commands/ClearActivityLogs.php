<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActivityLog;
class ClearActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-activity-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically deletes all records in the activity_logs table every month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = ActivityLog::count();
        ActivityLog::truncate();

        $this->info("Successfully deleted {$count} activity logs.");
    }
}
