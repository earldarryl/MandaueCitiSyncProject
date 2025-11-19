<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'grievance_id',
        'sender_id',
        'receiver_id',
        'message',
        'is_read',
        'file_path',
        'file_name',
    ];

    public function grievance()
    {
        return $this->belongsTo(Grievance::class, 'grievance_id', 'grievance_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function hasFile(): bool
    {
        return !empty($this->file_path);
    }

    public function fileUrl(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

}
