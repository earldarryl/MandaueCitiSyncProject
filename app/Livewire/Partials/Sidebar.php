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
                        ['label' => 'Citizens', 'route' => 'admin.stakeholders.citizens.index', 'icon' => 'bi bi-people-fill'],
                        ['label' => 'Departments & HR Liaisons', 'route' => 'admin.stakeholders.departments-and-hr-liaisons.index', 'icon' => 'bi bi-person-badge'],
                    ],
                ],
                [
                    'label' => 'Forms',
                    'icon' => 'bi bi-file-earmark-fill',
                    'children' => [
                        ['label' => 'Grievances', 'route' => 'admin.forms.grievances.index', 'icon' => 'bi bi-file-earmark-text'],
                        ['label' => 'Feedbacks', 'route' => 'admin.forms.feedbacks.index', 'icon' => 'bi bi-chat-right-dots-fill'],
                    ],
                ],
                [
                    'label' => 'Activity Logs',
                    'icon' => 'bi bi-clock-history',
                    'route' => 'admin.activity-logs.index',
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
                    'icon' => 'bi bi-people',
                    'children' => [
                        ['label' => 'Department Info', 'route' => 'hr-liaison.department.index', 'icon' => 'bi bi-building-fill-exclamation'],
                        ['label' => 'Grievance Repository', 'route' => 'hr-liaison.grievance.index', 'icon' => 'bi bi-archive-fill'],
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
                    'label' => 'My Grievances',
                    'icon'  => 'bi bi-file-earmark-text',
                    'route' => 'citizen.grievance.index',
                    'activePattern' => 'citizen.grievance.*',
                ],
                [
                    'label' => 'Feedback Form',
                    'icon' => 'bi bi-chat-right-dots-fill',
                    'route' => 'citizen.feedback-form',
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
