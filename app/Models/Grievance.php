<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grievance extends Model
{
    use HasFactory;

    protected $primaryKey = 'grievance_id';
    const PRIORITY_LOW = 'Low';
    const PRIORITY_NORMAL = 'Normal';
    const PRIORITY_HIGH = 'High';
    protected $fillable = [
        'user_id',
        'is_anonymous',
        'grievance_status',
        'priority_level',
        'grievance_type',
        'processing_days',
        'grievance_title',
        'grievance_details',
    ];

    public function attachments()
    {
        return $this->hasMany(GrievanceAttachment::class, 'grievance_id', 'grievance_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'grievance_id', 'grievance_id');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'assignments', 'grievance_id', 'department_id')
                    ->distinct();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'grievance_id', 'grievance_id');
    }
}
