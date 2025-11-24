<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
class Grievance extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'grievance_id';
    const PRIORITY_LOW = 'Low';
    const PRIORITY_NORMAL = 'Normal';
    const PRIORITY_HIGH = 'High';
    const PRIORITY_CRITICAL = 'Critical';

    protected $fillable = [
        'user_id',
        'is_anonymous',
        'grievance_status',
        'priority_level',
        'grievance_type',
        'grievance_category',
        'processing_days',
        'grievance_title',
        'grievance_details',
        'grievance_ticket_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($grievance) {
            $grievance->grievance_ticket_id = self::generateUniqueTicketId();
        });
    }

    private static function generateUniqueTicketId($length = 8)
    {
        do {
            $ticket = 'RPT-' . strtoupper(Str::random($length));
        } while (self::where('grievance_ticket_id', $ticket)->exists());

        return $ticket;
    }

    public function getRouteKeyName()
    {
        return 'grievance_ticket_id';
    }

    public function attachments()
    {
        return $this->hasMany(GrievanceAttachment::class, 'grievance_id', 'grievance_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'grievance_id', 'grievance_id');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'assignments', 'grievance_id', 'department_id')
                    ->distinct();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'grievance_id', 'grievance_id');
    }

    public function getFormattedCreatedAtAttribute(): ?string
    {
        return $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null;
    }

    public function getFormattedUpdatedAtAttribute(): ?string
    {
        return $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null;
    }

    public function editRequests()
    {
        return $this->hasMany(EditRequest::class, 'grievance_id', 'grievance_id');
    }

}
