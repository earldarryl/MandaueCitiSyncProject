<?php

namespace App\Console\Commands;

use App\Models\Grievance;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;
class MarkClosedGrievances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grievances:mark-closed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically close grievances after resolution or inactivity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info("Laravel Scheduler ran at " . now());

        $now = Carbon::now();

        $grievances = Grievance::whereIn('grievance_status', ['resolved', 'unresolved'])->get();

        $updatedCount = 0;

        foreach ($grievances as $grievance) {

            $autoCloseDate = $grievance->updated_at->copy()->addDays(7);

            if ($autoCloseDate->lte($now)) {

                $grievance->update(['grievance_status' => 'closed']);
                $updatedCount++;

                if ($grievance->user) {
                    $grievance->user->notify(new GeneralNotification(
                        'Grievance Closed',
                        "Your grievance titled '{$grievance->grievance_title}' has been automatically closed.",
                        'success',
                        ['grievance_ticket_id' => $grievance->grievance_ticket_id],
                        [],
                        true,
                        [
                            [
                                'label'        => 'View Grievance',
                                'url'          => route('citizen.grievance.view', $grievance->grievance_ticket_id),
                                'open_new_tab' => true,
                            ]
                        ]
                    ));
                }

                foreach ($grievance->assignedHrLiaisons() as $hr) {
                    $hr->notify(new GeneralNotification(
                        'Grievance Closed',
                        "A grievance titled '{$grievance->grievance_title}' assigned to your department has been auto-closed.",
                        'info',
                        ['grievance_ticket_id' => $grievance->grievance_ticket_id],
                        [],
                        true,
                        [
                            [
                                'label'        => 'View Grievance',
                                'url'          => route('hr-liaison.grievance.view', $grievance->grievance_ticket_id),
                                'open_new_tab' => true,
                            ]
                        ]
                    ));
                }

                foreach (User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get() as $admin) {
                    $admin->notify(new GeneralNotification(
                        'Grievance Closed Automatically',
                        "The grievance titled '{$grievance->grievance_title}' has been automatically closed.",
                        'info',
                        ['grievance_ticket_id' => $grievance->grievance_ticket_id],
                        [],
                        true,
                        [
                            [
                                'label'        => 'Open in Admin Panel',
                                'url'          => route('admin.forms.grievances.view', $grievance->grievance_ticket_id),
                                'open_new_tab' => true,
                            ]
                        ]
                    ));
                }

            }
        }

        $this->info("{$updatedCount} grievances automatically closed.");
    }

}
