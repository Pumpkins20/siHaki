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

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    const DOCUMENT_TYPES = [
        'main_document' => 'Dokumen Utama',
        'supporting_document' => 'Dokumen Pendukung',
        'certificate' => 'Sertifikat',
    ];

    const FILE_TYPE_NAMES = [
        'manual_document' => 'Manual Penggunaan Program',
        'video_file' => 'File Video',
        'ebook_file' => 'File E-book',
        'image_file' => 'File Gambar/Foto',
        'tool_photo' => 'Foto Alat Peraga',
        'metadata_file' => 'File Metadata',
        'additional_photos' => 'Foto Tambahan',
        'supporting_document' => 'Dokumen Pendukung',
        'main_document' => 'Dokumen Utama',
    ];

    public function submission()
    {
        return $this->belongsTo(HkiSubmission::class, 'submission_id');
    }

    public function getDocumentTypeNameAttribute()
    {
        return self::DOCUMENT_TYPES[$this->document_type] ?? $this->document_type;
    }

    public function getFileDisplayNameAttribute()
    {
        $fileName = $this->file_name;

        foreach (self::FILE_TYPE_NAMES as $type => $name) {
            if (strpos($fileName, $type) !== false) {
                return $name;
            }
        }

        return $this->document_type_name;
    }
}
