<?php

namespace App\Livewire\User\Admin\Stakeholders\Citizens;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\UserInfo;

#[Layout('layouts.app')]
#[Title('View Citizen')]
class View extends Component
{
    public $userInfo;
    public $user;

    public function mount($id)
    {
        $this->userInfo = UserInfo::with('user')->where('id', $id)->firstOrFail();
        $this->user = $this->userInfo->user;
    }

    public function render()
    {
        return view('livewire.user.admin.stakeholders.citizens.view');
    }
}
