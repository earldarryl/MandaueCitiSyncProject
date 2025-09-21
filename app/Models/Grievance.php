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
        'category',
        'grievance_status',
        'priority_level',
        'grievance_type',
        'processing_days',
        'grievance_title',
        'grievance_details',
    ];

    // Relationship to attachments
    public function attachments()
    {
        return $this->hasMany(GrievanceAttachment::class, 'grievance_id', 'grievance_id');
    }

    // Optional: relationship to assignments
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'grievance_id', 'grievance_id');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'assignments', 'grievance_id', 'department_id')
                    ->distinct();
    }

    // Optional: relationship to user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
