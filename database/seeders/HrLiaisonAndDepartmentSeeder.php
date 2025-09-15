<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class HrLiaisonAndDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'department_code' => 'PWH',
                'department_name' => 'Public Works & Highways',
                'department_description' => 'Handles public works and road matters',
                'is_active' => true,
                'is_available' => true,
            ],
            [
                'department_code' => 'IT',
                'department_name' => 'Information Technology',
                'department_description' => 'Handles technical and software issues',
                'is_active' => true,
                'is_available' => true,
            ],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(
                ['department_code' => $dept['department_code']],
                $dept
            );
        }
    }
}
