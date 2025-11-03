<?php

namespace App\Livewire\User\Admin\Stakeholders\DepartmentsAndHrLiaisons;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title(content: 'HR Liaisons List View')]
class HrLiaisonsListView extends Component
{
    public int $department;

    public function mount(int $department)
    {
        $this->department = $department;
    }

    public function render()
    {
        return view('livewire.user.admin.stakeholders.departments-and-hr-liaisons.hr-liaisons-list-view', [
            'department' => $this->department,
        ]);
    }
}
