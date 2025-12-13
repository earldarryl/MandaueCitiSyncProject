<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class ClearExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:clear-expired';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear user_id for expired sessions';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lifetime = config('session.lifetime');
        $expired = now()->subMinutes($lifetime)->timestamp;

        $updated = DB::table('sessions')
            ->where('last_activity', '<', $expired)
            ->whereNotNull('user_id')
            ->update(['user_id' => null]);

        $this->info("{$updated} expired sessions nulled.");
    }

}
