<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'document_type',
        'file_name',
        'file_path',
        'file_size',
        'uploaded_at',
    ];

    protected $dates = [
        'uploaded_at',
    ];

    const DOCUMENT_TYPES = [
        'main_document' => 'Main Document',
        'supporting_document' => 'Supporting Document',
        'certificate' => 'Certificate',
    ];

    public function submission()
    {
        // Perbaikan: Spesifikasi foreign key yang benar
        return $this->belongsTo(HkiSubmission::class, 'submission_id');
    }
}
