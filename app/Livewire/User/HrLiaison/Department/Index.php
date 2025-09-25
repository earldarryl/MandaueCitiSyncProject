<?php

namespace App\Livewire\User\HrLiaison\Department;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Department')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.user.hr-liaison.department.index');
    }
}
