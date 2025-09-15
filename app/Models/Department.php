<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    /** @use HasFactory<\Database\Factories\DepartmentFactory> */
    use HasFactory;

    protected $primaryKey = 'department_id';
    protected $fillable = [
        'department_name',
        'department_code',
        'department_description',
        'is_active',
        'is_available',
    ];
    public function hrLiaisons()
    {
        return $this->belongsToMany(
            User::class,
            'hr_liaison_department',
            'department_id',
            'hr_liaison_id',
            'department_id',
            'id'
        );
    }

}
