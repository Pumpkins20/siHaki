<?php

namespace App\Helpers;

class StatusHelper
{
    /**
     * Get consistent status colors across the application
     */
    public static function getStatusColors()
    {
        return [
            'draft' => 'secondary',           // Abu-abu
            'submitted' => 'primary',         // Biru
            'under_review' => 'info',         // Biru muda
            'revision_needed' => 'warning',   // Kuning
            'approved' => 'success',          // Hijau
            'rejected' => 'danger',           // Merah
        ];
    }

    /**
     * Get status icons
     */
    public static function getStatusIcons()
    {
        return [
            'draft' => 'file-earmark-text',
            'submitted' => 'clock',
            'under_review' => 'eye',
            'revision_needed' => 'arrow-clockwise',
            'approved' => 'check-circle',
            'rejected' => 'x-circle',
        ];
    }

    /**
     * Get human readable status names
     */
    public static function getStatusNames()
    {
        return [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'revision_needed' => 'Revision Needed',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
    }

    /**
     * Get status color for specific status
     */
    public static function getStatusColor($status)
    {
        return self::getStatusColors()[$status] ?? 'secondary';
    }

    /**
     * Get status icon for specific status
     */
    public static function getStatusIcon($status)
    {
        return self::getStatusIcons()[$status] ?? 'question-circle';
    }

    /**
     * Get status name for specific status
     */
    public static function getStatusName($status)
    {
        return self::getStatusNames()[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }
}