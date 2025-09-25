<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrLiaisonDepartment extends Model
{
    use HasFactory;

    protected $table = 'hr_liaison_department';

    protected $primaryKey = 'id'; // or your custom key if you have one

    protected $fillable = [
        'hr_liaison_id',
        'department_id',
    ];

    // HR Liaison relationship
    public function hrLiaison()
    {
        return $this->belongsTo(User::class, 'hr_liaison_id');
    }

    // Department relationship
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }
}
