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
                'department_code' => 'BPLO',
                'department_name' => 'Business Permit and Licensing Office',
                'department_description' => 'Handles business registration, permit renewals, and regulatory compliance in Mandaue City.',
                'is_active' => true,
                'is_available' => true,
            ],
            [
                'department_code' => 'TEAM',
                'department_name' => 'Traffic Enforcement Agency of Mandaue',
                'department_description' => 'Manages traffic regulation, enforcement of road safety laws, and vehicle violation processing.',
                'is_active' => true,
                'is_available' => true,
            ],
            [
                'department_code' => 'CSWS',
                'department_name' => 'City Social Welfare Services',
                'department_description' => 'Provides social assistance, welfare programs, and community support for vulnerable sectors.',
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
