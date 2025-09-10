<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class HrLiaisonAndDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        // Make sure the hr_liaison role exists
        $role = Role::findOrCreate('hr_liaison', 'web');

        // Example HR Liaison users + departments
        $data = [
            [
                'user' => [
                    'name' => 'Alice HR',
                    'email' => 'alice.hr@example.com',
                    'password' => Hash::make('password123'),
                ],
                'department' => [
                    'department_code' => 'FIN',
                    'department_name' => 'Finance & Budgeting',
                    'department_description' => 'Handles financing and budgeting matters',
                ]
            ],
            [
                'user' => [
                    'name' => 'Bob HR',
                    'email' => 'bob.hr@example.com',
                    'password' => Hash::make('password123'),
                ],
                'department' => [
                    'department_code' => 'HRD',
                    'department_name' => 'Human Resources',
                    'department_description' => 'Manages employee relations',
                ]
            ],
        ];

        foreach ($data as $item) {
            // Create HR user if not exists
            $user = User::firstOrCreate(
                ['email' => $item['user']['email']],
                array_merge($item['user'], [
                    'contact' => '09123456789',
                    'email_verified_at' => now(),
                    'agreed_terms' => true,
                    'terms_version' => '1.0',
                    'agreed_at' => now(),
                ])
            );

            // Assign hr_liaison role
            if (!$user->hasRole('hr_liaison')) {
                $user->assignRole($role);
            }

            // Create or update department
            Department::updateOrCreate(
                ['department_code' => $item['department']['department_code']],
                array_merge($item['department'], [
                    'hr_user_id' => $user->id,
                    'is_active' => true,
                    'is_available' => true,
                ])
            );
        }
    }
}
