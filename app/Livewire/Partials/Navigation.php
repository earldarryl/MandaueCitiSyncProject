<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
class Navigation extends Component
{
    public $user;
    public $userName;
    public $userEmail;
    public $status;

    public $unreadNotifications;
    public $readNotifications;

    public $unreadCount = 0;
    public $readCount = 0;

    public $unreadLimit = 20;
    public $readLimit = 20;
    public $header = 'Dashboard';
    protected $listeners = [
        'user-profile-updated' => 'refreshUserData',
        'notification-created'  => 'prependNewNotification',
        'refreshNotifications'  => 'loadCounts',
        'updateUnreadCount'     => 'loadCounts',
    ];
    private $routeHeaders = [

        'dashboard'          => 'Dashboard',
        'activity-logs.index' => 'Activity Logs',
        'users.citizens'     => 'Citizen Users',
        'users.hr-liaisons'  => 'HR Liaisons Users',

        'hr-liaison.dashboard'           => 'Dashboard',
        'hr-liaison.activity-logs.index' => 'Activity Logs',
        'hr-liaison.department.index' => 'Department Info',
        'hr-liaison.department.view' => 'Department Info View',
        'hr-liaison.grievance.index' => 'Grievance List',
        'hr-liaison.grievance.edit'     => 'Edit Grievance',
        'hr-liaison.grievance.view' => 'View Grievance',

        'citizen.grievance.index'    => 'My Grievances',
        'citizen.grievance.create'   => 'File a Grievance',
        'citizen.grievance.edit'     => 'Edit Grievance',
        'citizen.grievance.view'     => 'View Grievance',
        'citizen.feedback-form'     => 'Feedback Form',
        'citizen.submission-history'     => 'Submission History',

        'settings'              => 'Settings',
        'settings.profile'      => 'Profile Settings',
        'settings.appearance'   => 'Appearance Settings',
        'settings.two-factor-auth' => 'Two-Factor Authentication',
        'password.confirm'      => 'Confirm Password',
        'sidebar'               => 'Sidebar',
    ];

    public function mount()
    {
        $this->user = Auth::user();
        $this->unreadNotifications = collect();
        $this->readNotifications   = collect();

        if ($this->user) {
            $this->userName  = $this->user->name;
            $this->userEmail = $this->user->email;
            $this->updateStatus();
            $this->loadCounts();
        }

        $this->setHeader();
    }
    public function getHeaderIconClass()
    {
        $icons = [
            'Dashboard' => 'bi bi-speedometer2',
            'Activity Logs' => 'bi bi-journal-text',
            'Citizen Users' => 'bi bi-people',
            'HR Liaisons Users' => 'bi bi-person-badge',
            'Department Info' => 'bi bi-building-fill-exclamation',
            'Department Info View' => 'bi bi-building-fill-exclamation',

            'My Grievances' => 'bi bi-file-earmark-text',
            'File a Grievance' => 'bi bi-file-earmark-plus',
            'Edit Grievance' => 'bi bi-pencil-square',
            'View Grievance' => 'bi bi-eye',
            'Feedback Form' => 'bi bi-chat-right-dots-fill',
            'Submission History' => 'bi bi-clock-history',
            'Grievance List' => 'bi bi-file-earmark-text',

            'Settings' => 'bi bi-gear',
            'Profile Settings' => 'bi bi-person-circle',
            'Appearance Settings' => 'bi bi-palette',
            'Two-Factor Authentication' => 'bi bi-shield-lock',
            'Confirm Password' => 'bi bi-lock',
            'Sidebar' => 'bi bi-list',
        ];

        return $icons[$this->header] ?? 'bi bi-circle';
    }

    public function hydrate()
    {
        $this->loadCounts();
        $this->updateStatus();
    }

    private function setHeader()
    {
        $currentRoute = Route::currentRouteName();

        $this->header = $this->routeHeaders[$currentRoute]
            ?? ucfirst(str_replace('.', ' ', $currentRoute));
    }
    public function getUserStatus()
    {
        if (!$this->user || !$this->user->last_seen_at) {
            return ['text' => 'Offline', 'color' => 'text-red-500'];
        }

        $diff = now()->diffInMinutes($this->user->last_seen_at);

        if ($diff <= 5) return ['text' => 'Online', 'color' => 'text-green-500'];

        return ['text' => 'Away', 'color' => 'text-yellow-500'];
    }

    public function updateStatus()
    {
        $this->status = $this->getUserStatus();
    }
    public function loadCounts()
    {
        if (!$this->user) return;

        $this->unreadCount = $this->user->unreadNotifications()->count();
        $this->readCount   = $this->user->readNotifications()->count();
    }
    public function prependNewNotification($notification = null)
    {
        if (!$notification) return;

        $this->unreadCount++;
    }
    public function refreshUserData($data)
    {
        $this->userName  = $data['name']  ?? $this->userName;
        $this->userEmail = $data['email'] ?? $this->userEmail;
    }

    public function render()
    {
        return view('livewire.partials.navigation');
    }
}
