<?php

namespace App\Livewire\User\Admin\Dashboard;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Filament\Notifications\Notification;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Index extends Component
{
    public $users = [];
    public $user;
    public $userModel;

    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->user = auth()->user();

        if (Gate::allows('viewAny', User::class)) {
            $this->users = User::all();
            $this->userModel = User::class;
        }

        // optional: initialize dates
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');

        if (session()->pull('just_logged_in', false)) {
            Notification::make()
                ->title('Welcome back, ' . $this->user->name . ' 👋')
                ->body('Good to see you again! Here’s your dashboard.')
                ->success()
                ->send();

            $this->dispatch('notification-created');
        }
    }

    public function render()
    {
        return view('livewire.user.admin.dashboard.index', [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}

