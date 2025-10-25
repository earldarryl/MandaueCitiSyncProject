<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class ActivityLogsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $userId = 98;
        $roleId = DB::table('model_has_roles')->where('model_id', $userId)->value('role_id');

        $grievanceIds = DB::table('grievances')->pluck('grievance_id')->toArray();

        $actions = [
            'Viewed Grievance',
            'Assigned Grievance',
            'Updated Grievance Status',
            'Commented on Grievance',
            'Archived Grievance',
        ];

        foreach ($grievanceIds as $grievanceId) {
            DB::table('activity_logs')->insert([
                'user_id'        => $userId,
                'role_id'        => $roleId,
                'action'         => $actions[array_rand($actions)],
                'timestamp'      => Carbon::now()->subMinutes(rand(1, 300)),
                'ip_address'     => '192.168.0.' . rand(1, 255),
                'device_info'    => 'Browser ' . rand(80, 110),
            ]);
        }
    }
}
