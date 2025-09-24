<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;

class HrLiaisonSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['code' => 'HR', 'name' => 'Human Resources', 'description' => 'Handles employee grievances and relations'],
            ['code' => 'FIN', 'name' => 'Finance', 'description' => 'Manages financial concerns and budgeting'],
            ['code' => 'OPS', 'name' => 'Operations', 'description' => 'Handles operations and logistics'],
            ['code' => 'IT', 'name' => 'Information Technology', 'description' => 'Maintains IT systems and support'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['department_code' => $dept['code']],
                [
                    'department_name' => $dept['name'],
                    'department_description' => $dept['description'],
                    'is_active' => true,
                    'is_available' => true,
                ]
            );
        }

        $liaisons = [
            [
                'email' => 'jane.liaison@example.com',
                'name' => 'Jane Doe',
                'departments' => ['HR'],
                'sort_order' => 2,
            ],
            [
                'email' => 'john.liaison@example.com',
                'name' => 'John Smith',
                'departments' => ['FIN', 'OPS'],
                'sort_order' => 3,
            ],
            [
                'email' => 'emma.liaison@example.com',
                'name' => 'Emma Johnson',
                'departments' => ['HR', 'IT'],
                'sort_order' => 4,
            ],
            [
                'email' => 'mike.liaison@example.com',
                'name' => 'Mike Brown',
                'departments' => ['HR'],
                'sort_order' => 5,
            ],
        ];

        foreach ($liaisons as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('password123'),
                    'email_verified_at' => now(),
                    'sort_order' => $data['sort_order'],
                ]
            );

            $user->assignRole('hr_liaison');

            $deptIds = Department::whereIn('department_code', $data['departments'])
                ->pluck('department_id')
                ->toArray();

            $user->departments()->syncWithoutDetaching($deptIds);
        }
    }
}
