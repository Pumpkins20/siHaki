<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HkiSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reviewer_id',
        'title',
        'type',
        'creation_type',
        'description',
        'status',
        'submission_date',
        'reviewed_at',
        'review_notes',
        'additional_data',
        'member_count',
    ];

    protected $casts = [
        'submission_date' => 'datetime',
        'reviewed_at' => 'datetime',
        'additional_data' => 'array',
    ];

    const CREATION_TYPES = [
        'program_komputer' => 'Program Komputer',
        'sinematografi' => 'Sinematografi',
        'buku' => 'Buku',
        'poster_fotografi' => 'Poster / Fotografi / Seni Gambar / Karakter Animasi',
        'alat_peraga' => 'Alat Peraga',
        'basis_data' => 'Basis Data',
    ];

    const STATUSES = [
        'draft' => 'Draft',
        'submitted' => 'Submitted',
        'under_review' => 'Under Review',
        'revision_needed' => 'Revision Needed',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function documents()
    {
        return $this->hasMany(SubmissionDocument::class, 'submission_id');
    }

    public function histories()
    {
        return $this->hasMany(SubmissionHistory::class, 'submission_id');
    }

    public function members()
    {
        return $this->hasMany(SubmissionMember::class, 'submission_id')->orderBy('position');
    }

    public function leader()
    {
        return $this->hasOne(SubmissionMember::class)->where('is_leader', true);
    }

    // Accessors
    public function getCreationTypeNameAttribute()
    {
        return self::CREATION_TYPES[$this->creation_type] ?? $this->type ?? 'Unknown';
    }

    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'draft' => 'secondary',
            'submitted' => 'info',
            'under_review' => 'primary',
            'revision_needed' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
        ];
        
        return $colors[$this->status] ?? 'secondary';
    }

    public function getFormattedSubmissionDateAttribute()
    {
        return $this->submission_date ? $this->submission_date->format('d M Y H:i') : '-';
    }

    public function getFormattedReviewedAtAttribute()
    {
        return $this->reviewed_at ? $this->reviewed_at->format('d M Y H:i') : '-';
    }
}
