<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'user_id',
        'date',
        'gender',
        'region',
        'service',
        'cc1',
        'cc2',
        'cc3',
        'answers',
        'suggestions',
        'email',
        'cc_summary',
        'sqd_summary',
    ];

    protected $casts = [
        'answers' => 'array',
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

     protected static function booted()
    {
        static::saving(function ($feedback) {
            $feedback->cc_summary = static::computeCCSummary($feedback);
            $feedback->sqd_summary = static::computeSQDSummary($feedback);
        });
    }

    public static function computeCCSummary($feedback): string
    {
        $ccFields = ['cc1', 'cc2', 'cc3'];
        $responses = [];

        foreach ($ccFields as $field) {
            if (isset($feedback->$field)) {
                $responses[] = (int)$feedback->$field;
            }
        }

        if (empty($responses)) return 'No CC responses';

        $counts = array_count_values($responses);

        $categories = [
            'High Awareness' => $counts[1] ?? 0,
            'Medium Awareness' => $counts[2] ?? 0,
            'Low Awareness' => $counts[3] ?? 0,
            'No Awareness' => $counts[4] ?? 0,
            'N/A' => $counts[5] ?? 0,
        ];

        $maxCount = max($categories);
        $dominant = array_keys($categories, $maxCount);

        if (in_array('High Awareness', $dominant)) return 'High Awareness';
        if (in_array('Medium Awareness', $dominant)) return 'Medium Awareness';
        if (in_array('Low Awareness', $dominant)) return 'Low Awareness';
        if (in_array('No Awareness', $dominant)) return 'No Awareness';
        return 'N/A';
    }

    public static function computeSQDSummary($feedback): string
    {
        $answers = is_string($feedback->answers)
            ? json_decode($feedback->answers, true) ?: []
            : (is_array($feedback->answers) ? $feedback->answers : []);

        if (empty($answers)) return 'No answers';

        $counts = array_count_values($answers);

        $categories = [
            'Strongly Disagree' => $counts[1] ?? 0,
            'Disagree' => $counts[2] ?? 0,
            'Neither' => $counts[3] ?? 0,
            'Agree' => $counts[4] ?? 0,
            'Strongly Agree' => $counts[5] ?? 0,
            'N/A' => $counts[6] ?? 0,
        ];

        $maxCount = max($categories);
        $dominant = array_keys($categories, $maxCount);

        if (in_array('Strongly Agree', $dominant) || in_array('Agree', $dominant)) return 'Most Agree';
        if (in_array('Strongly Disagree', $dominant) || in_array('Disagree', $dominant)) return 'Most Disagree';
        if (in_array('Neither', $dominant)) return 'Neutral';
        return 'N/A';
    }
}
