<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('activitylogs:clear-old', function () {
    ActivityLog::query()->delete();

    $this->info('✅ All activity logs have been cleared.');

    Log::info('Activity logs table cleared automatically on schedule.');
})->describe('Clear all activity logs monthly');

app()->booted(function () {
    $schedule = app(Schedule::class);

    $schedule->command('activitylogs:clear-old')->monthlyOn(1, '00:00');
});
