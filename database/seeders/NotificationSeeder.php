<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // Pick a user to assign notifications
        $user = User::find(90); // adjust if you want a specific user

        if (!$user) {
            $this->command->info("No users found. Create a user first.");
            return;
        }

        // Seed 50 notifications: 30 unread, 20 read
        for ($i = 1; $i <= 50; $i++) {
            $isRead = $i > 30; // first 30 unread, rest read

            DB::table('notifications')->insert([
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\TestNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'title' => "Test Notification #{$i}",
                    'message' => "This is a sample notification for testing."
                ]),
                'read_at' => $isRead ? Carbon::now()->subDays(rand(0, 5)) : null,
                'created_at' => Carbon::now()->subDays(rand(0, 10))->subMinutes(rand(0, 60)),
                'updated_at' => Carbon::now(),
            ]);
        }

        $this->command->info("50 notifications created for user: {$user->email}");
    }
}
