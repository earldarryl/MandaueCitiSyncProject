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
        'hr_user_id',
        'department_name',
        'department_code',
        'department_description',
        'is_active',
        'is_available',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($department) {
            if (!$department->hrUser || !$department->hrUser->hasAnyRole(['hr_liaison'])) {
                throw new \Exception("Assigned user must have HR Liaison role.");
            }
        });
    }
    public function hrUser()
    {
        return $this->belongsTo(User::class, 'hr_user_id');
    }
}
