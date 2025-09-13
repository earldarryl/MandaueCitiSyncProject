<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Flux\Flux;

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

    public $activeTab = 'unread';

    protected $listeners = [
        'user-profile-updated' => 'refreshUserData',
        'notification-created' => 'prependNewNotification',
    ];

    public function mount()
    {
        $this->user = Auth::user();

        if ($this->user) {
            $this->userName = $this->user->name;
            $this->userEmail = $this->user->email;
            $this->updateStatus();
            $this->loadNotifications();
        }
    }

    public function getUserStatus()
    {
        if (!$this->user || !$this->user->last_seen_at) {
            return ['text' => 'Offline', 'color' => 'text-red-500'];
        }

        $diff = now()->diffInMinutes($this->user->last_seen_at);

        if ($diff <= 5) {
            return ['text' => 'Online', 'color' => 'text-green-500'];
        }

        return ['text' => 'Away', 'color' => 'text-gray-500'];
    }

    public function updateStatus()
    {
        $this->status = $this->getUserStatus();
    }

    public function loadNotifications()
    {
        if (!$this->user) return;

        $this->unreadNotifications = $this->user
            ->unreadNotifications()
            ->latest()
            ->take($this->unreadLimit)
            ->get();

        $this->readNotifications = $this->user
            ->readNotifications()
            ->latest()
            ->take($this->readLimit)
            ->get();

        $this->unreadCount = $this->user->unreadNotifications()->count();
        $this->readCount = $this->user->readNotifications()->count();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;

        // reset limits when switching tabs
        if ($tab === 'unread') {
            $this->unreadLimit = 20;
        } elseif ($tab === 'read') {
            $this->readLimit = 20;
        }

        $this->loadNotifications();
    }

    public function loadMore(string $type)
    {
        if ($type === 'unread') {
            $this->unreadLimit += 20;
        } else {
            $this->readLimit += 20;
        }

        $this->loadNotifications();
    }

    public function prependNewNotification($notification)
    {
        $this->unreadNotifications->prepend($notification);
        $this->unreadCount++;
    }

    public function markNotificationAsRead(string $id)
    {
        if (!$this->user) return;

        $notification = $this->user->notifications()->find($id);
        if (!$notification) return;

        if ($notification->read_at === null) {
            $notification->markAsRead();

            // Update UI collections
            $this->unreadNotifications = $this->unreadNotifications->reject(fn($n) => $n->id === $id);
            $this->readNotifications->prepend($notification);

            $this->unreadCount--;
            $this->readCount++;
        }

        Flux::modal('openModalNotif-' . $id)->close();
    }


    public function deleteNotification(string $id)
    {
        if (!$this->user) return;

        $notification = $this->user->notifications()->find($id);
        if (!$notification) return;

        $isUnread = $notification->read_at === null;
        $notification->delete();

        if ($isUnread) {
            $this->unreadNotifications = $this->unreadNotifications->reject(fn($n) => $n->id === $id);
            $this->unreadCount--;
        } else {
            $this->readNotifications = $this->readNotifications->reject(fn($n) => $n->id === $id);
            $this->readCount--;
        }

        $this->dispatch('notifications-updated');
        Flux::modal('openModalNotif-' . $id)->close();
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
