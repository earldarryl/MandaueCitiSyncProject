<?php

namespace App\Livewire\Partials;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Navigation extends Component
{
    public $user;
    public $userName;
    public $userEmail;
    public $notifications = [];
    public $unreadCount = 0;

    protected $listeners = [
        'user-profile-updated' => 'refreshUserData',
        'notification-created' => 'loadNotifications',
    ];

    public function mount()
    {
        $this->user = auth()->user();

        if ($this->user) {
            $this->userName = $this->user->name;
            $this->userEmail = $this->user->email;
            $this->loadNotifications();
        }
    }

    public function loadNotifications()
    {
        if ($this->user) {
            $this->notifications = $this->user->notifications()
                ->latest()
                ->take(5)
                ->get();

            $this->unreadCount = $this->user->unreadNotifications()->count();
        }
    }

    public function markAsRead($id)
    {
        $notification = $this->user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

    public function refreshUserData($updatedData)
    {
        $this->userName = $updatedData['name'];
        $this->userEmail = $updatedData['email'];
    }

    public function render()
    {
        return view('livewire.partials.navigation');
    }
}
