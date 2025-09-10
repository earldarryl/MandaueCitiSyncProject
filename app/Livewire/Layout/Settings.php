<?php

namespace App\Livewire\Layout;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Settings extends Component
{
    public function render()
    {
        return view('livewire.layout.settings');
    }
}
