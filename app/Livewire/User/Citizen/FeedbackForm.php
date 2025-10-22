<?php

namespace App\Livewire\User\Citizen;

use App\Models\Feedback;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Client Satisfaction Measurement')]
class FeedbackForm extends Component
{
    public $date;
    public $gender;
    public $region;
    public $service;
    public $cc1;
    public $cc2;
    public $cc3;
    public $answers = [];
    public $suggestions;
    public $email;

    protected $rules = [
        'date' => 'required|date',
        'gender' => 'nullable|string|max:50',
        'region' => 'nullable|string|max:100',
        'service' => 'nullable|string|max:255',
        'cc1' => 'nullable|string|max:10',
        'cc2' => 'nullable|string|max:10',
        'cc3' => 'nullable|string|max:10',
        'answers' => 'nullable|array',
        'email' => 'nullable|email|max:255',
    ];

    public function submit()
    {
        $this->validate();

        Feedback::create([
            'date' => $this->date,
            'gender' => $this->gender,
            'region' => $this->region,
            'service' => $this->service,
            'cc1' => $this->cc1,
            'cc2' => $this->cc2,
            'cc3' => $this->cc3,
            'answers' => json_encode($this->answers),
            'suggestions' => $this->suggestions,
            'email' => $this->email,
        ]);

        $this->reset();

        Notification::make()
            ->title('Feedback submitted successfully!')
            ->body('✅ Thank you for your time and valuable input.')
            ->success()
            ->duration(5000)
            ->send();
    }

    public function render()
    {
        return view('livewire.user.citizen.feedback-form');
    }
}
