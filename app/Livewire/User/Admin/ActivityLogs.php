<?php

namespace App\Livewire\User\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Activity Logs')]
class ActivityLogs extends Component
{
    public function render()
    {
        return view('livewire.user.admin.activity-logs');
    }
}
