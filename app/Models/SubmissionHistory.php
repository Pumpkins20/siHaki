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

    /**
     * Relationship dengan HkiSubmission
     */
    public function submission()
    {
        return $this->belongsTo(HkiSubmission::class, 'submission_id');
    }

    /**
     * Relationship dengan User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get action color for UI
     */
    public function getActionColorAttribute()
    {
        return match($this->action) {
            'Approved' => 'success',
            'Rejected' => 'danger',
            'Revision Requested' => 'warning',
            'Submitted' => 'info',
            'Assigned for Review' => 'primary',
            default => 'secondary'
        };
    }
}
