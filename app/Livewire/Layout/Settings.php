<?php

namespace App\Livewire\Layout;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
#[Layout('layouts.app')]
#[Title('User Settings')]
class Settings extends Component
{
    public function render()
    {
        return view('livewire.layout.settings');
    }
}
