<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class Department extends Model
{
    /** @use HasFactory<\Database\Factories\DepartmentFactory> */
    use HasFactory;

    protected $primaryKey = 'department_id';
    protected $fillable = [
        'department_name',
        'department_code',
        'department_description',
        'department_bg',
        'department_profile',
        'is_active',
        'is_available',
    ];
    public function hrLiaisons()
    {
        return $this->belongsToMany(
            User::class,
            'hr_liaison_departments',
            'department_id',
            'hr_liaison_id',
            'department_id',
            'id'
        );
    }

    public function getHrLiaisonsStatusAttribute(): string
    {
        $total = $this->hrLiaisons()->count();
        $active = $this->hrLiaisons()->get()->filter(fn($user) => $user->isOnline())->count();

        return "$active / $total";
    }

    public function getDepartmentBgUrlAttribute()
    {
        return $this->department_bg
            ? Storage::url($this->department_bg)
            : asset('images/default-department-bg.jpg');
    }

    public function getDepartmentProfileUrlAttribute()
    {
        return $this->department_profile
            ? Storage::url($this->department_profile)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->department_name) . '&background=random';
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'department_id', 'department_id');
    }
}
