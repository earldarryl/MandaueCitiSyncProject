<?php

namespace App\Livewire\PrintFiles;

use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.prints')]
#[Title('Print All Feedbacks')]
class PrintAllFeedbacks extends Component
{
    public $feedbacks;
    public $admin;

    public function mount()
    {
        $user = Auth::user();
        $this->admin = $user;

        $this->feedbacks = Feedback::with(['user'])
            ->latest()
            ->get()
            ->map(function ($feedback) {
                $feedback->cc_summary = $this->summarizeCC($feedback);
                $feedback->sqd_summary = $this->summarizeSQD($feedback->answers);
                return $feedback;
            });
    }

    public function summarizeCC($feedback)
    {
        $ccFields = ['cc1', 'cc2', 'cc3'];
        $responses = [];

        foreach ($ccFields as $field) {
            if (isset($feedback->$field)) {
                $responses[] = (int) $feedback->$field;
            }
        }

        if (empty($responses)) return 'No CC responses provided.';

        $counts = array_count_values($responses);

        $categories = [
            'High Awareness' => Arr::get($counts, 1, 0),
            'Medium Awareness' => Arr::get($counts, 2, 0),
            'Low Awareness' => Arr::get($counts, 3, 0),
            'No Awareness' => Arr::get($counts, 4, 0),
            'N/A' => Arr::get($counts, 5, 0),
        ];

        $maxCount = max($categories);
        $dominantCategories = array_keys($categories, $maxCount);

        if (in_array('High Awareness', $dominantCategories)) return 'This feedback shows strong awareness of the CC.';
        if (in_array('Medium Awareness', $dominantCategories)) return 'This feedback shows moderate awareness of the CC.';
        if (in_array('Low Awareness', $dominantCategories)) return 'This feedback shows low awareness of the CC.';
        if (in_array('No Awareness', $dominantCategories)) return 'This feedback shows no awareness of the CC.';

        return 'This feedback has no applicable CC summary.';
    }

    public function summarizeSQD($answers)
    {
        if (!is_array($answers)) $answers = json_decode($answers, true) ?: [];

        if (empty($answers)) return 'No answers provided';

        $counts = array_count_values($answers);

        $categories = [
            'Strongly Disagree' => Arr::get($counts, 1, 0),
            'Disagree' => Arr::get($counts, 2, 0),
            'Neither' => Arr::get($counts, 3, 0),
            'Agree' => Arr::get($counts, 4, 0),
            'Strongly Agree' => Arr::get($counts, 5, 0),
            'N/A' => Arr::get($counts, 6, 0),
        ];

        $maxCount = max($categories);
        $dominantCategories = array_keys($categories, $maxCount);

        if (in_array('Strongly Agree', $dominantCategories) || in_array('Agree', $dominantCategories)) {
            return 'This feedback expresses satisfaction as it is answered mostly ' . implode(' / ', $dominantCategories) . '.';
        }

        if (in_array('Strongly Disagree', $dominantCategories) || in_array('Disagree', $dominantCategories)) {
            return 'This feedback expresses dissatisfaction as it is answered mostly ' . implode(' / ', $dominantCategories) . '.';
        }

        if (in_array('Neither', $dominantCategories)) return 'This feedback expresses neutrality as it is answered mostly Neither.';

        return 'This feedback cannot be summarized.';
    }

    public function render()
    {
        return view('livewire.print-files.print-all-feedbacks', [
            'feedbacks' => $this->feedbacks,
            'admin' => $this->admin,
        ]);
    }
}
