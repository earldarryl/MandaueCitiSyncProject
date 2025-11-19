<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'activity_log_id';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'role_id',
        'module',
        'action',
        'action_type',
        'model_type',
        'model_id',
        'description',
        'changes',
        'status',
        'ip_address',
        'device_info',
        'user_agent',
        'platform',
        'location',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'changes' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class);
    }

}
