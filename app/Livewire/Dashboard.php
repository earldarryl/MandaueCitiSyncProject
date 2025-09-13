<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Session;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public $users = [];
    public $user;
    public $userModel;

   public function mount()
    {
        $this->user = auth()->user();

        if (Gate::allows('viewAny', User::class)) {
            $this->users = User::all();
            $this->userModel = User::class;
        }

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
        return view('livewire.dashboard');
    }
}
