<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrievanceReroute extends Model
{
        use HasFactory;

    protected $fillable = [
        'grievance_id',
        'from_department_id',
        'to_department_id',
        'performed_by',
        'from_category',
        'to_category',
    ];

    public function grievance()
    {
        return $this->belongsTo(Grievance::class, 'grievance_id', 'grievance_id');
    }

    public function fromDepartment()
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment()
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

}
