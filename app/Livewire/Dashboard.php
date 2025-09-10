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

    if (! session()->has('welcome_back_shown') && ! session()->pull('just_registered', false)) {
        Notification::make()
            ->title('Welcome back, ' . $this->user->name . ' 👋')
            ->body('Good to see you again! Here’s your dashboard.')
            ->success()
            ->send();

        $this->dispatch('notification-created');

        session()->put('welcome_back_shown', true);
    }
}



    public function render()
    {
        return view('livewire.dashboard');
    }
}
