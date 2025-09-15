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
                    'icon' => 'bi bi-house',
                    'route' => 'dashboard',
                ],
                [
                    'label' => 'Department',
                    'icon' => 'bi bi-people',
                    'children' => [
                        ['label' => 'Department Info', 'route' => 'grievance.create','icon' => 'bi bi-info-circle-fill',],
                        ['label' => 'Grievance Repository', 'route' => 'grievance.create','icon' => 'bi bi-archive-fill',],


                    ],
                ],
                [
                    'label' => 'Users',
                    'icon' => 'bi bi-person-fill',
                    'children' => [
                        ['label' => 'Citizens', 'route' => 'users.citizens','icon' => 'bi bi-people-fill',],
                        ['label' => 'HR Liaisons', 'route' => 'users.hr-liaisons','icon' => 'bi bi-person-badge',],


                    ],
                ],
                [
                    'label' => 'Feedback Form',
                    'icon' => 'bi bi-chat-right-dots-fill',
                    'route' => 'dashboard',
                ],
                [
                    'label' => 'Activity Logs',
                    'icon' => 'bi bi-clock-history',
                    'route' => 'activity-logs.index',
                ],
            ];
        }
        elseif ($user->hasRole('hr_liaison')) {
            $this->menuItems = [
                [
                    'label' => 'Dashboard',
                    'icon' => 'bi bi-house',
                    'route' => 'dashboard',
                ],
                 [
                    'label' => 'Department',
                    'icon' => 'bi bi-people',
                    'children' => [
                        ['label' => 'Department Info', 'route' => 'grievance.create','icon' => 'bi bi-info-circle-fill',],
                        ['label' => 'Grievance Repository', 'route' => 'grievance.create','icon' => 'bi bi-archive-fill',],


                    ],
                ],
                [
                    'label' => 'Feedback Form',
                    'icon' => 'bi bi-chat-right-dots-fill',
                    'route' => 'dashboard',
                ],
                [
                    'label' => 'Activity Logs',
                    'icon' => 'bi bi-clock-history',
                    'route' => 'dashboard',
                ],
            ];
        }
        elseif ($user->hasRole('citizen')){
            $this->menuItems = [
                 [
                    'label' => 'Grievance',
                    'icon' => 'bi bi-house',
                    'route' => 'grievance.create',
                ],
                [
                    'label' => 'FAQs',
                    'icon' => 'bi bi-question-circle-fill',
                    'route' => 'dashboard',
                ],
                [
                    'label' => 'Feedback Form',
                    'icon' => 'bi bi-chat-right-dots-fill',
                    'route' => 'dashboard',
                ],
            ];

        }
    }
    public function render()
    {
        return view('livewire.partials.sidebar');
    }
}
