<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nidn',
        'nama',
        'username',
        'email',
        'password',
        'program_studi',
        'foto',
        'role',
        'phone',
        'department_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Constants for program_studi values
     */
    const PROGRAM_STUDI_OPTIONS = [
        'D3 Manajemen Informatika',
        'S1 Informatika',
        'S1 Sistem Informasi',
        'S1 Teknologi Informasi',
    ];

    /**
     * Constants for role values
     */
    const ROLE_OPTIONS = [
        'admin' => 'Administrator',
        'pengguna' => 'Pengguna',
        'user' => 'User',
        'reviewer' => 'Reviewer',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(HkiSubmission::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(HkiSubmission::class, 'reviewer_id');
    }

    public function submissionHistories(): HasMany
    {
        return $this->hasMany(SubmissionHistory::class);
    }
}
