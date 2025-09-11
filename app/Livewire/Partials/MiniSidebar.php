<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class MiniSidebar extends Component
{
    public array $menuItems = [];


    public function mount()
    {
        $this->menuItems = [
            [
                'label' => 'Profile',
                'icon' => 'bi bi-person-lines-fill',
                'route' => 'profile',
            ],
            [
                'label' => 'Two Factor Authentication',
                'icon' => 'bi bi-gear-fill',
                'route' => 'two-factor-auth',
            ]
        ];
    }
    public function render()
    {
        return view('livewire.partials.mini-sidebar');
    }
}
