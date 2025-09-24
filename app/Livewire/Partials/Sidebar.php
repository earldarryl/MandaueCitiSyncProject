<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Sidebar extends Component
{
    public array $menuItems = [];

    public function mount()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $this->menuItems = [
                [
                    'label' => 'Dashboard',
                    'icon' => 'bi bi-speedometer2',
                    'route' => 'admin.dashboard',
                ],
                [
                    'label' => 'Users',
                    'icon' => 'bi bi-person-fill',
                    'children' => [
                        ['label' => 'Citizens', 'route' => 'admin.users.citizens', 'icon' => 'bi bi-people-fill'],
                        ['label' => 'HR Liaisons', 'route' => 'admin.users.hr-liaisons', 'icon' => 'bi bi-person-badge'],
                    ],
                ],
                [
                    'label' => 'Activity Logs',
                    'icon' => 'bi bi-clock-history',
                    'route' => 'admin.activity-logs.index',
                ],
            ];
        }
        elseif ($user->hasRole('hr_liaison')) {
            $this->menuItems = [
                [
                    'label' => 'Dashboard',
                    'icon' => 'bi bi-speedometer2',
                    'route' => 'hr-liaison.dashboard',
                ],
                 [
                    'label' => 'Department',
                    'icon' => 'bi bi-people',
                    'children' => [
                        ['label' => 'Department Info', 'route' => 'hr-liaison.department.index', 'icon' => 'bi bi-info-circle-fill'],
                        ['label' => 'Grievance Repository', 'route' => 'hr-liaison.grievance.index', 'icon' => 'bi bi-archive-fill'],
                    ],
                ],
                [
                    'label' => 'Activity Logs',
                    'icon' => 'bi bi-clock-history',
                    'route' => 'hr-liaison.activity-logs.index',
                ],
            ];
        }
        elseif ($user->hasRole('citizen')){
            $this->menuItems = [
                [
                    'label' => 'My Grievances',
                    'icon'  => 'bi bi-file-earmark-text',
                    'route' => 'citizen.grievance.index',
                    'activePattern' => 'citizen.grievance.*',
                ],
                [
                    'label' => 'FAQs',
                    'icon' => 'bi bi-question-circle-fill',
                    'route' => 'citizen.grievance.index', // or create a dedicated route if you have one
                ],
                [
                    'label' => 'Feedback Form',
                    'icon' => 'bi bi-chat-right-dots-fill',
                    'route' => 'citizen.grievance.index', // or assign a real feedback form route
                ],
            ];

        }
    }
    public function render()
    {
        return view('livewire.partials.sidebar');
    }
}
