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
        'mime_type',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'file_size' => 'integer',
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

    /**
     * Relationship dengan HkiSubmission
     */
    public function submission()
    {
        return $this->belongsTo(HkiSubmission::class, 'submission_id');
    }

    /**
     * Get document type name
     */
    public function getDocumentTypeNameAttribute()
    {
        return self::DOCUMENT_TYPES[$this->document_type] ?? $this->document_type;
    }

    /**
     * Get file display name based on file name pattern
     */
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

    /**
     * Get file size in human readable format
     */
    public function getFileSizeHumanAttribute()
    {
        $size = $this->file_size;
        
        if ($size >= 1073741824) {
            return number_format($size / 1073741824, 2) . ' GB';
        } elseif ($size >= 1048576) {
            return number_format($size / 1048576, 2) . ' MB';
        } elseif ($size >= 1024) {
            return number_format($size / 1024, 2) . ' KB';
        } else {
            return $size . ' bytes';
        }
    }

    /**
     * Get file extension
     */
    public function getFileExtensionAttribute()
    {
        return strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
    }

    /**
     * Check if file is previewable
     */
    public function getIsPreviewableAttribute()
    {
        $previewableTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];
        return in_array($this->file_extension, $previewableTypes);
    }

    /**
     * Get storage path
     */
    public function getStoragePathAttribute()
    {
        return storage_path('app/private/submissions/' . $this->file_path);
    }

    /**
     * Check if file exists
     */
    public function fileExists()
    {
        return file_exists($this->storage_path);
    }
}
