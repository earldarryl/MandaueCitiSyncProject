<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $primaryKey = 'assignment_id';

    protected $fillable = [
        'grievance_id',
        'department_id',
        'hr_liaison_id',
        'assigned_at',
    ];

    public function grievance()
    {
        return $this->belongsTo(Grievance::class, 'grievance_id', 'grievance_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function hrLiaisons()
    {
        return $this->belongsToMany(
            User::class,
            'hr_liaison_department',
            'department_id',
            'hr_liaison_id'
        )->withTimestamps();
    }
}
