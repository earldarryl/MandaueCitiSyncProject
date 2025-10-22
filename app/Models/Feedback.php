<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
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
    ];

    protected $casts = [
        'answers' => 'array',
        'date' => 'date',
    ];
}
