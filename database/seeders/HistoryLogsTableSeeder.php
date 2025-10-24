<?php

namespace Database\Seeders;

use App\Models\HistoryLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class HistoryLogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('history_logs')->truncate();

        $logs = [
            [
                'user_id' => 102,
                'action_type' => 'Submission',
                'description' => 'Submitted a grievance about delayed service response.',
                'reference_table' => 'grievances',
                'reference_id' => 160,
                'ip_address' => '192.168.1.12',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'user_id' => 102,
                'action_type' => 'Feedback',
                'description' => 'Provided feedback for HR liaison performance.',
                'reference_table' => 'feedbacks',
                'reference_id' => 1,
                'ip_address' => '192.168.1.12',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => 102,
                'action_type' => 'Feedback',
                'description' => 'Provided feedback for HR liaison performance.',
                'reference_table' => 'feedbacks',
                'reference_id' => 2,
                'ip_address' => '192.168.1.12',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
        ];

        HistoryLog::insert($logs);
    }

}
