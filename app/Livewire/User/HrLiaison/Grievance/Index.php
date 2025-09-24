<?php

namespace App\Livewire\User\HrLiaison\Grievance;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Grievance Reports')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.user.hr-liaison.grievance.index');
    }
}
