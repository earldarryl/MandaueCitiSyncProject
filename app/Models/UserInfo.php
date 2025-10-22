<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    protected $fillable = [
        'user_id',
        'first_name', 'middle_name', 'last_name', 'suffix',
        'gender', 'civil_status', 'barangay',
        'sitio', 'birthdate', 'age',
        'phone_number',
        'emergency_contact_name', 'emergency_contact_number', 'emergency_relationship',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
