<?php

namespace App\Livewire\User\Citizen;

use App\Models\Feedback;
use App\Models\HistoryLog;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
#[Layout('layouts.app')]
#[Title('Client Satisfaction Measurement')]
class FeedbackForm extends Component
{
    public $showConfirmModal = false;
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
        'gender' => 'required|string|max:50',
        'region' => 'required|string|max:100',
        'service' => 'required|string|max:255',
        'cc1' => 'required|string|max:10',
        'cc2' => 'required|string|max:10',
        'cc3' => 'required|string|max:10',
        'answers' => 'nullable|array',
        'email' => 'nullable|email|max:255',
    ];

    public function submit()
    {
        try {
            $this->validate();

            $feedback = Feedback::create([
                'user_id' => auth()->id(),
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

            HistoryLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'feedback_submission',
                'description' => 'Submitted a client satisfaction feedback form.',
                'reference_id' => $feedback->id,
                'reference_table' => 'feedback',
                'ip_address' => request()->ip(),
            ]);

            $this->reset();

            Notification::make()
                ->title('Feedback submitted successfully!')
                ->body('Thank you for your time and valuable input.')
                ->success()
                ->duration(5000)
                ->send();

        } catch (ValidationException $e) {
            $this->showConfirmModal = true;

            throw $e;
        }
    }

    public function resetForm()
    {
        $this->date = null;
        $this->gender = null;
        $this->region = null;
        $this->service = null;
        $this->cc1 = null;
        $this->cc2 = null;
        $this->cc3 = null;
        $this->answers = [];
        $this->suggestions = null;
        $this->email = null;
    }

    public function render()
    {
        return view('livewire.user.citizen.feedback-form');
    }
}
