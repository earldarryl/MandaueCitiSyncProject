<?php

namespace App\Livewire\User\Admin\ActivtiyLogs;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
#[Layout('layouts.app')]
#[Title('Activity Logs')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.user.admin.activtiy-logs.index');
    }
}
