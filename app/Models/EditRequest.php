<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EditRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'grievance_id',
        'user_id',
        'status',
        'reason',
    ];

    public function grievance()
    {
        return $this->belongsTo(Grievance::class, 'grievance_id', 'grievance_id');
    }

    /**
     * Relationship: The user who requested the edit.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope for pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for denied requests.
     */
    public function scopeDenied($query)
    {
        return $query->where('status', 'denied');
    }
}
