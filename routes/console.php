<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use App\Models\Grievance;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
Artisan::command('inspire', function () {
    $this->comment(\Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('activitylogs:clear-old', function () {
    ActivityLog::query()->delete();
    $this->info('All activity logs have been cleared.');
    Log::info('Activity logs table cleared automatically on schedule.');
})->describe('Clear all activity logs monthly');

Artisan::command('grievances:mark-overdue', function () {
    $now = Carbon::now();

    $activeStatuses = ['pending', 'acknowledged', 'in_progress', 'escalated'];

    $grievances = Grievance::whereIn('grievance_status', $activeStatuses)
        ->whereNotNull('processing_days')
        ->get();

    $updatedCount = 0;

    foreach ($grievances as $grievance) {
        $dueDate = $grievance->created_at->copy()->addDays($grievance->processing_days);

        if ($dueDate->lte($now)) {
            $grievance->grievance_status = 'overdue';
            $grievance->save();
            $updatedCount++;
        }
    }

    $this->info("{$updatedCount} grievances marked as overdue.");
    Log::info("{$updatedCount} grievances automatically marked as overdue at {$now}.");
})->describe('Mark grievances as overdue if processing_days exceeded.');

app()->booted(function () {
    $schedule = app(Schedule::class);
    $schedule->command('grievances:mark-overdue')->everyMinute();
    $schedule->command('grievances:mark-closed')->everyMinute();
    $schedule->command('activitylogs:clear-old')->monthlyOn(1, '00:00');
    $schedule->command('sessions:clear-expired')->hourly();
});

