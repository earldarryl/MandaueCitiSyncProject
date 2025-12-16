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
                    'label' => 'Stakeholders',
                    'icon' => 'bi bi-person-fill',
                    'children' => [
                        ['label' => 'Citizens', 'route' => 'admin.stakeholders.citizens.index', 'icon' => 'bi bi-people-fill', 'activePattern' => 'admin.stakeholders.citizens.*',],
                        ['label' => 'Departments & HR Liaisons', 'route' => 'admin.stakeholders.departments-and-hr-liaisons.index', 'icon' => 'bi bi-person-badge', 'activePattern' => 'admin.stakeholders.departments-and-hr-liaisons.*',],
                    ],
                ],
                [
                    'label' => 'Forms',
                    'icon' => 'bi bi-file-earmark-fill',
                    'children' => [
                        ['label' => 'Reports', 'route' => 'admin.forms.grievances.index', 'icon' => 'bi bi-file-earmark-text', 'activePattern' => 'admin.forms.grievances.*',],
                        ['label' => 'Feedbacks', 'route' => 'admin.forms.feedbacks.index', 'icon' => 'bi bi-chat-right-dots-fill', 'activePattern' => 'admin.forms.feedbacks.*',],
                    ],

                ],
                [
                    'label' => 'Activity Logs',
                    'icon' => 'bi bi-clock-history',
                    'route' => 'admin.activtiy-logs.index',
                ],
                [
                    'label' => 'Reports & Analytics',
                    'icon' => 'bi bi-file-bar-graph',
                    'route' => 'admin.reports-and-analytics.index',
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
                    'icon' => 'bi bi-building-gear',
                    'children' => [
                        ['label' => 'Department Info', 'route' => 'hr-liaison.department.index', 'icon' => 'bi bi-building-fill-exclamation', 'activePattern' => 'hr-liaison.department.*',],
                        ['label' => 'Assignment Reports', 'route' => 'hr-liaison.grievance.index', 'icon' => 'bi bi-archive-fill', 'activePattern' => 'hr-liaison.grievance.*',],
                    ],

                ],
                [
                    'label' => 'Activity Logs',
                    'icon' => 'bi bi-clock-history',
                    'route' => 'hr-liaison.activity-logs.index',
                ],
                [
                    'label' => 'Reports & Analytics',
                    'icon' => 'bi bi-file-bar-graph',
                    'route' => 'hr-liaison.reports-and-analytics.index',
                ],
            ];
        }
        elseif ($user->hasRole('citizen')){
            $this->menuItems = [
                [
                    'label' => 'My Reports',
                    'icon'  => 'bi bi-file-earmark-text',
                    'route' => 'citizen.grievance.index',
                    'activePattern' => 'citizen.grievance.*',
                ],
                [
                    'label' => 'Submission History',
                    'icon' => 'bi bi-clock-history',
                    'route' => 'citizen.submission-history',
                ],
            ];

        }
    }
    public function render()
    {
        return view('livewire.partials.sidebar');
    }
}
