<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\HrLiaisonDepartment;

class HrLiaisonDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $hrLiaisons = User::role('hr_liaison')->get();

        $departments = Department::all();

        foreach ($hrLiaisons as $liaison) {
            $randomDepartments = $departments->random(2);
            foreach ($randomDepartments as $dept) {
                HrLiaisonDepartment::firstOrCreate([
                    'hr_liaison_id' => $liaison->id,
                    'department_id' => $dept->department_id,
                ]);
            }
        }
    }
}
