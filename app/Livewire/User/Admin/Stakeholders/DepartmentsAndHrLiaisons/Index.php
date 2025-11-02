<?php

namespace App\Livewire\User\Admin\Stakeholders\DepartmentsAndHrLiaisons;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Departments & HrLiaisons')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.user.admin.stakeholders.departments-and-hr-liaisons.index');
    }
}
