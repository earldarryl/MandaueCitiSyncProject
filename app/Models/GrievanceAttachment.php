<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrievanceAttachment extends Model
{
    use HasFactory;

    protected $primaryKey = 'attachment_id';

    protected $fillable = ['grievance_id', 'file_path'];

    public function grievance()
    {
        return $this->belongsTo(Grievance::class, 'grievance_id', 'grievance_id');
    }
}
