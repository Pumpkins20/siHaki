<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'user_id',
        'action',
        'previous_status',
        'new_status',
        'notes',
    ];

    public function submission()
    {
        // Perbaikan: Spesifikasi foreign key yang benar
        return $this->belongsTo(HkiSubmission::class, 'submission_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
