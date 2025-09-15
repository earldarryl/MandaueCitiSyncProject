<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    // Set custom primary key
    protected $primaryKey = 'assignment_id';

    // Mass assignable fields
    protected $fillable = [
        'grievance_id',
        'department_id',
        'hr_liaison_id', // new field
        'assigned_at',
    ];

    // Grievance relationship
    public function grievance()
    {
        return $this->belongsTo(Grievance::class, 'grievance_id', 'grievance_id');
    }

    // Department relationship
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    // HR Liaison relationship
    public function hrLiaison()
    {
        return $this->belongsTo(User::class, 'hr_liaison_id');
    }
}
