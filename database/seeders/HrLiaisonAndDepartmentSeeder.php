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
                'department_code' => 'HR',
                'department_name' => 'Human Resources',
                'department_description' => 'Manages employee relations, recruitment, and organizational policies.',
                'is_active' => true,
                'is_available' => true,
            ],
            [
                'department_code' => 'FIN',
                'department_name' => 'Finance',
                'department_description' => 'Oversees budgeting, accounting, and financial reporting activities.',
                'is_active' => true,
                'is_available' => true,
            ],
            [
                'department_code' => 'PRC',
                'department_name' => 'Procurement',
                'department_description' => 'Responsible for purchasing goods, services, and managing supplier relations.',
                'is_active' => true,
                'is_available' => true,
            ],
            [
                'department_code' => 'LEG',
                'department_name' => 'Legal Affairs',
                'department_description' => 'Handles legal compliance, contracts, and organizational documentation.',
                'is_active' => true,
                'is_available' => true,
            ],
            [
                'department_code' => 'HSP',
                'department_name' => 'Health Services',
                'department_description' => 'Provides medical assistance and manages healthcare programs.',
                'is_active' => true,
                'is_available' => true,
            ],
            [
                'department_code' => 'EDU',
                'department_name' => 'Education',
                'department_description' => 'Supervises academic programs, training, and learning initiatives.',
                'is_active' => true,
                'is_available' => true,
            ],
            [
                'department_code' => 'ADM',
                'department_name' => 'Administration',
                'department_description' => 'Handles general operations, logistics, and internal coordination.',
                'is_active' => true,
                'is_available' => true,
            ],
            [
                'department_code' => 'SEC',
                'department_name' => 'Security',
                'department_description' => 'Ensures safety, protection, and compliance with security protocols.',
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
