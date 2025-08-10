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
        'email',
        'whatsapp',
        'alamat', // ✅ NEW: alamat column
        'kode_pos', // ✅ NEW: kode_pos column
        'ktp',
        'position',
        'is_leader',
    ];

    protected $casts = [
        'is_leader' => 'boolean',
        'position' => 'integer',
    ];

    /**
     * Relationship dengan HkiSubmission
     */
    public function submission()
    {
        return $this->belongsTo(HkiSubmission::class, 'submission_id');
    }

    /**
     * Get KTP file path
     */
    public function getKtpPathAttribute()
    {
        if (!$this->ktp) {
            return null;
        }
        // ✅ FIX: Update path sesuai dengan storage actual
        return storage_path('app/public/' . $this->ktp);
    }

    /**
     * Check if KTP file exists
     */
    public function ktpExists()
    {
        return $this->ktp && file_exists($this->ktp_path);
    }

    /**
     * Get KTP public URL (if accessible via storage link)
     */
    public function getKtpUrlAttribute()
    {
        if (!$this->ktp) {
            return null;
        }
        return asset('storage/' . $this->ktp);
    }

    /**
     * Get formatted WhatsApp number for URL
     */
    public function getWhatsappUrlAttribute()
    {
        return 'https://wa.me/62' . ltrim($this->whatsapp, '0');
    }

    /**
     * Get position label
     */
    public function getPositionLabelAttribute()
    {
        if ($this->is_leader) {
            return 'Ketua';
        }
        return 'Anggota ' . $this->position;
    }

    /**
     * Scope untuk leader
     */
    public function scopeLeader($query)
    {
        return $query->where('is_leader', true);
    }

    /**
     * Scope untuk members (non-leader)
     */
    public function scopeMembers($query)
    {
        return $query->where('is_leader', false);
    }

    /**
     * ✅ NEW: Get formatted address
     */
    public function getFormattedAddressAttribute()
    {
        if (!$this->alamat && !$this->kode_pos) {
            return '-';
        }
        
        $address = $this->alamat ?: '';
        if ($this->kode_pos) {
            $address .= ($address ? ' ' : '') . $this->kode_pos;
        }
        
        return $address;
    }
}
