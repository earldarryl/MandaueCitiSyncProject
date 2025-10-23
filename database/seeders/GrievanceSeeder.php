<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grievance;
use App\Models\Department;
use App\Models\Assignment;
use App\Models\User;

class GrievanceSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::whereHas('hrLiaisons')->get();

        $citizens = User::whereHas('roles', fn($q) => $q->where('name', 'citizen'))->get();

        if ($departments->isEmpty() || $citizens->isEmpty()) {
            $this->command->warn('⚠️  No departments with HR liaisons or citizens found. Skipping grievance seeding.');
            return;
        }

        foreach (range(1, 10) as $i) {
            $user = $citizens->random();
            $selectedDepartments = $departments->random(rand(1, 2));

            $grievance = Grievance::create([
                'user_id'          => $user->id,
                'grievance_type'   => fake()->randomElement(['Complaint', 'Request', 'Inquiry']),
                'priority_level'   => fake()->randomElement(['Low', 'Normal', 'High']),
                'grievance_title'  => fake()->sentence(6),
                'grievance_details'=> fake()->paragraph(4),
                'is_anonymous'     => fake()->boolean(20),
                'grievance_status' => fake()->randomElement(['pending', 'in_progress', 'resolved', 'rejected']),
                'processing_days'  => fake()->numberBetween(0, 10),
                'created_at'       => now()->subDays(rand(0, 30)),
                'updated_at'       => now(),
            ]);

            foreach ($selectedDepartments as $dept) {
                $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
                    ->whereHas('departments', fn($q) => $q->where('hr_liaison_departments.department_id', $dept->department_id))
                    ->get();

                foreach ($hrLiaisons as $hr) {
                    Assignment::create([
                        'grievance_id'  => $grievance->grievance_id,
                        'department_id' => $dept->department_id,
                        'assigned_at'   => now(),
                        'hr_liaison_id' => $hr->id,
                    ]);
                }
            }
        }

        $this->command->info('Successfully seeded 10 grievances with HR assignments.');
    }
}
