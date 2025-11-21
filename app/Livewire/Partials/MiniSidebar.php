<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class MiniSidebar extends Component
{
    public array $menuItems = [];

    public function mount()
    {
        $user = Auth::user();
        $roleName = strtolower($user?->roles->first()?->name ?? '');

        $this->menuItems = [
            [
                'label' => 'Profile',
                'icon' => 'bi bi-person-lines-fill',
                'route' => 'profile',
            ],
        ];

        if (!in_array($roleName, ['citizen'])) {
            $this->menuItems[] = [
                'label' => 'Two Factor Authentication',
                'icon' => 'bi bi-gear-fill',
                'route' => 'two-factor-auth',
            ];
        }
    }

    public function render()
    {
        return view('livewire.partials.mini-sidebar');
    }
}
