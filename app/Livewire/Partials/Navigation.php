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
        'refreshNotifications' => 'refreshNotifications',
    ];
    private $routeHeaders = [

        'dashboard'          => 'Dashboard',
        'activity-logs.index' => 'Activity Logs',
        'users.citizens'     => 'Citizen Users',
        'users.hr-liaisons'  => 'HR Liaisons Users',

        'hr-liaison.dashboard'           => 'Dashboard',
        'hr-liaison.activity-logs.index' => 'Activity Logs',

        'hr-liaison.grievance.index' => 'Grievance List',
        'hr-liaison.grievance.view' => 'View Grievance',

        'citizen.grievance.index'    => 'My Grievances',
        'citizen.grievance.create'   => 'File a Grievance',
        'citizen.grievance.edit'     => 'Edit Grievance',
        'citizen.grievance.view'     => 'View Grievance',

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
            $this->loadNotifications();
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

            'My Grievances' => 'bi bi-file-earmark-text',
            'File a Grievance' => 'bi bi-plus-square',
            'Edit Grievance' => 'bi bi-pencil-square',
            'View Grievance' => 'bi bi-eye',
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
    /** ------------------ STATUS ------------------ */
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

    /** ------------------ NOTIFICATIONS ------------------ */
    public function loadNotifications()
    {
        if (!$this->user) return;

        $this->unreadNotifications = $this->user
            ->unreadNotifications()
            ->latest('created_at')
            ->take($this->unreadLimit)
            ->get();

        $this->readNotifications = $this->user
            ->readNotifications()
            ->latest('created_at')
            ->take($this->readLimit)
            ->get();

        $this->loadCounts();
    }

    public function refreshNotifications()
    {
        $this->loadNotifications();
    }

    private function loadCounts()
    {
        if (!$this->user) return;

        $this->unreadCount = $this->user->unreadNotifications()->count();
        $this->readCount   = $this->user->readNotifications()->count();
    }

    public function loadMore($type = 'all')
    {
        if (!$this->user) return;

        if ($type === 'unread' || $type === 'all') {
            $moreUnread = $this->user
                ->unreadNotifications()
                ->latest('created_at')
                ->skip($this->unreadNotifications->count())
                ->take(20)
                ->get();
            $this->unreadNotifications = $this->unreadNotifications->concat($moreUnread);
            $this->unreadLimit += 20;
        }

        if ($type === 'read' || $type === 'all') {
            $moreRead = $this->user
                ->readNotifications()
                ->latest('created_at')
                ->skip($this->readNotifications->count())
                ->take(20)
                ->get();
            $this->readNotifications = $this->readNotifications->concat($moreRead);
            $this->readLimit += 20;
        }

        $this->loadCounts();
    }

    public function markNotificationAsRead($id)
    {
        $notification = $this->user->notifications()->where('id', $id)->first();
        if (!$notification || !is_null($notification->read_at)) return;

        $notification->markAsRead();

        $this->unreadNotifications = $this->unreadNotifications->reject(fn($n) => $n->id === $id);
        $fresh = $this->user->notifications()->where('id', $id)->first();
        if ($fresh) $this->readNotifications->prepend($fresh);

        $this->loadCounts();
    }

    public function markNotificationAsUnread($id)
    {
        $notification = $this->user->notifications()->where('id', $id)->first();
        if (!$notification || is_null($notification->read_at)) return;

        $notification->update(['read_at' => null]);

        $this->readNotifications = $this->readNotifications->reject(fn($n) => $n->id === $id);
        $fresh = $this->user->notifications()->where('id', $id)->first();
        if ($fresh) $this->unreadNotifications->prepend($fresh);

        $this->loadCounts();
    }

    public function deleteNotification($id)
    {
        $notif = $this->user->notifications()->where('id', $id)->first();
        if (!$notif) return;

        $notif->delete();

        $this->unreadNotifications = $this->unreadNotifications->reject(fn($n) => $n->id === $id);
        $this->readNotifications   = $this->readNotifications->reject(fn($n) => $n->id === $id);

        $this->loadCounts();
    }

    /** ------------------ BULK ACTIONS ------------------ */
    public function markAllAsRead()
    {
        $this->user->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        $this->loadNotifications();
    }

    public function markAllAsUnread()
    {
        $this->user->notifications()->whereNotNull('read_at')->update(['read_at' => null]);
        $this->loadNotifications();
    }

    public function deleteAllUnread()
    {
        $this->user->notifications()->whereNull('read_at')->delete();
        $this->loadNotifications();
    }

    public function deleteAllRead()
    {
        $this->user->notifications()->whereNotNull('read_at')->delete();
        $this->loadNotifications();
    }

    /** ------------------ LISTENER HELPERS ------------------ */
    public function prependNewNotification($notification = null)
    {
        if (!$this->user || !$notification) return;

        if (is_array($notification)) $notification = (object)$notification;
        if (isset($notification->notifiable_id) && $notification->notifiable_id !== $this->user->id) return;

        $this->unreadNotifications->prepend($notification);
        $this->unreadCount++;
    }

    public function refreshUserData($updatedData)
    {
        $this->userName  = $updatedData['name']  ?? $this->userName;
        $this->userEmail = $updatedData['email'] ?? $this->userEmail;
    }

    public function render()
    {
        return view('livewire.partials.navigation');
    }
}
