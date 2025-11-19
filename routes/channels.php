<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Grievance;

Broadcast::channel('grievance.{grievance_id}', function ($user, $grievance_id) {
    $grievance = Grievance::with('assignments')->find($grievance_id);

    if (!$grievance) {
        return false;
    }

    if ($grievance->user_id === $user->id) {
        return true;
    }

    if ($grievance->assignments->contains('hr_liaison_id', $user->id)) {
        return true;
    }

    if ($user->hasRole('admin')) {
        return true;
    }

    return false;
});


Broadcast::channel('App.Models.User.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
