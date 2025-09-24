<?php

namespace App\Livewire\User\HrLiaison\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.user.hr-liaison.dashboard.index');
    }
}
