<?php

namespace App\Console\Commands;

use App\Models\Grievance;
use Illuminate\Console\Command;
use Carbon\Carbon;
class MarkOverdueGrievances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grievances:mark-overdue';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically mark grievances as overdue if processing days have passed';
    /**
     * Execute the console command.
     */
    public function handle()
{
    $now = Carbon::now();

    $grievances = Grievance::whereIn('grievance_status', ['pending','acknowledged','in_progress','escalated'])
        ->whereNotNull('processing_days')
        ->get();

    $updatedCount = 0;

    foreach ($grievances as $grievance) {
        $deadline = $grievance->created_at->copy()->addDays((int)$grievance->processing_days);

        echo "Grievance {$grievance->grievance_ticket_id} | created_at: {$grievance->created_at} | processing_days: {$grievance->processing_days} | deadline: {$deadline} | now: {$now}\n";

        if ($deadline->lte($now)) {
            $grievance->update(['grievance_status' => 'overdue']);
            echo " => MARKED OVERDUE\n";
        } else {
            echo " => NOT DUE YET\n";
        }
    }


    $this->info("{$updatedCount} grievances marked as overdue.");
}

}
