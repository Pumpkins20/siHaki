<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'name',
        'whatsapp',
        'email',
        'ktp',
        'position',
        'is_leader',
    ];

    protected $casts = [
        'is_leader' => 'boolean',
    ];

    public function submission()
    {
        return $this->belongsTo(HkiSubmission::class);
    }
}
