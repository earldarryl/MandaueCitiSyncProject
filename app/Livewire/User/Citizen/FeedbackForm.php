<?php

namespace App\Livewire\User\Citizen;

use App\Models\ActivityLog;
use App\Models\Feedback;
use App\Models\HistoryLog;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
#[Layout('layouts.app')]
#[Title('Feedback Form')]
class FeedbackForm extends Component
{
    public $showConfirmModal = false;
    public $date;
    public $gender;
    public $region = "Region VII - Central Visayas";
    public $service = "Report Management System";
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
        'answers' => 'required|array|min:1',
        'answers.*' => 'required',
        'email' => 'nullable|email|max:255',
    ];

    protected $messages = [
        'date.required' => 'Please select a date.',
        'date.date' => 'The date must be a valid date.',

        'gender.required' => 'Please select your gender.',
        'gender.string' => 'Invalid value for gender.',
        'gender.max' => 'Gender must not exceed 50 characters.',

        'region.required' => 'Region of residence is required.',
        'region.string' => 'Invalid value for region.',
        'region.max' => 'Region must not exceed 100 characters.',

        'service.required' => 'Service availed is required.',
        'service.string' => 'Invalid value for service.',
        'service.max' => 'Service must not exceed 255 characters.',

        'cc1.required' => 'Please answer CC1.',
        'cc1.string' => 'Invalid value for CC1.',
        'cc1.max' => 'CC1 must not exceed 10 characters.',

        'cc2.required' => 'Please answer CC2.',
        'cc2.string' => 'Invalid value for CC2.',
        'cc2.max' => 'CC2 must not exceed 10 characters.',

        'cc3.required' => 'Please answer CC3.',
        'cc3.string' => 'Invalid value for CC3.',
        'cc3.max' => 'CC3 must not exceed 10 characters.',

        'answers.required' => 'Please answer all SQD questions.',
        'answers.*.required' => 'This question is required.',

        'email.email' => 'Please enter a valid email address.',
        'email.max' => 'Email must not exceed 255 characters.',
    ];

    public string $ticket;

    public function mount($ticket)
    {
        if (session()->has("feedback_used_{$ticket}")) {
            abort(403, 'This feedback link has already been used.');
        }

        $this->ticket = $ticket;

        $user = auth()->user();

        if ($user && $user->hasRole('citizen') && $user->userInfo) {
            $this->gender = $user->userInfo->gender;
        }

        $this->date = now()->format('Y-m-d');
    }

    public function submit()
    {
        try {
            $this->validate();

            $ccResponses = [(int)$this->cc1, (int)$this->cc2, (int)$this->cc3];
            $ccCounts = array_count_values($ccResponses);

            $ccCategories = [
                'High Awareness' => $ccCounts[1] ?? 0,
                'Medium Awareness' => $ccCounts[2] ?? 0,
                'Low Awareness' => $ccCounts[3] ?? 0,
                'No Awareness' => $ccCounts[4] ?? 0,
                'N/A' => $ccCounts[5] ?? 0,
            ];

            $maxCC = max($ccCategories);
            $dominantCC = array_keys($ccCategories, $maxCC);
            if (in_array('High Awareness', $dominantCC)) $ccSummary = 'High Awareness';
            elseif (in_array('Medium Awareness', $dominantCC)) $ccSummary = 'Medium Awareness';
            elseif (in_array('Low Awareness', $dominantCC)) $ccSummary = 'Low Awareness';
            elseif (in_array('No Awareness', $dominantCC)) $ccSummary = 'No Awareness';
            else $ccSummary = 'N/A';

            $answersArray = is_array($this->answers)
                ? $this->answers
                : (json_decode($this->answers, true) ?: []);
            $answerCounts = array_count_values($answersArray);

            $sqdCategories = [
                'Strongly Disagree' => $answerCounts[1] ?? 0,
                'Disagree' => $answerCounts[2] ?? 0,
                'Neither' => $answerCounts[3] ?? 0,
                'Agree' => $answerCounts[4] ?? 0,
                'Strongly Agree' => $answerCounts[5] ?? 0,
                'N/A' => $answerCounts[6] ?? 0,
            ];

            $maxSQD = max($sqdCategories);
            $dominantSQD = array_keys($sqdCategories, $maxSQD);
            if (in_array('Strongly Agree', $dominantSQD) || in_array('Agree', $dominantSQD)) $sqdSummary = 'Most Agree';
            elseif (in_array('Strongly Disagree', $dominantSQD) || in_array('Disagree', $dominantSQD)) $sqdSummary = 'Most Disagree';
            elseif (in_array('Neither', $dominantSQD)) $sqdSummary = 'Neutral';
            else $sqdSummary = 'N/A';

            $feedback = Feedback::create([
                'user_id' => auth()->id(),
                'date' => $this->date,
                'gender' => $this->gender,
                'region' => $this->region,
                'service' => $this->service,
                'cc1' => $this->cc1,
                'cc2' => $this->cc2,
                'cc3' => $this->cc3,
                'cc_summary' => $ccSummary,
                'answers' => json_encode($this->answers),
                'sqd_summary' => $sqdSummary,
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

            $user = auth()->user();
            $roleName = ucfirst($user->roles->first()?->name ?? 'User');

            ActivityLog::create([
                'user_id'      => $user->id,
                'role_id'      => $user->roles->first()?->id,
                'module'       => 'Feedback System',
                'action'       => "Submitted feedback #{$feedback->id}",
                'action_type'  => 'create',
                'model_type'   => Feedback::class,
                'model_id'     => $feedback->id,
                'description'  => "{$roleName} ({$user->email}) submitted a feedback form.",
                'changes'      => $feedback->toArray(),
                'status'       => 'success',
                'ip_address'   => request()->ip(),
                'device_info'  => request()->header('User-Agent'),
                'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
                'platform'     => php_uname('s'),
                'location'     => geoip(request()->ip())?->city,
                'timestamp'    => now(),
            ]);

            $sender = auth()->user();

            $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();

            foreach ($admins as $admin) {
                $admin->notify(new GeneralNotification(
                    'New Feedback Submitted',
                    "{$sender->name} submitted feedback #{$feedback->id}.",
                    'info',
                    ['feedback_id' => $feedback->id],
                    ['type' => 'info'],
                    true,
                    [
                        [
                            'label'        => 'View Feedback',
                            'url'          => route('admin.forms.feedbacks.view', $feedback->id),
                            'open_new_tab' => true,
                        ]
                    ]
                ));
            }

            auth()->user()->notify(new GeneralNotification(
                'Feedback Submitted',
                "Your feedback has been received and will be reviewed.",
                'success',
                ['feedback_id' => $feedback->id],
                ['type' => 'success'],
                true,
                []
            ));

            $this->cc1 = null;
            $this->cc2 = null;
            $this->cc3 = null;
            $this->answers = [];
            $this->suggestions = null;
            $this->email = null;

            session()->put("feedback_used_{$this->ticket}", true);

        } catch (ValidationException $e) {
            $this->showConfirmModal = true;
            throw $e;
        }
    }

    public function resetForm()
    {
        $this->cc1 = null;
        $this->cc2 = null;
        $this->cc3 = null;
        $this->answers = [];
        $this->suggestions = null;
        $this->email = null;

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Feedback Form Resetted',
            'message' => 'All fields cleared successfully',
        ]);

    }

    public function render()
    {
        return view('livewire.user.citizen.feedback-form');
    }
}
