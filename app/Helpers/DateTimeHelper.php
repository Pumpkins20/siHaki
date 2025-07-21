<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateTimeHelper
{
    /**
     * Get current time in WIB timezone
     */
    public static function nowWIB()
    {
        return Carbon::now('Asia/Jakarta');
    }

    /**
     * Format date to WIB timezone
     */
    public static function formatToWIB($date, $format = 'd M Y H:i')
    {
        if (!$date) {
            return '-';
        }

        return Carbon::parse($date)->setTimezone('Asia/Jakarta')->format($format);
    }

    /**
     * Format date with WIB suffix
     */
    public static function formatWithWIB($date, $format = 'd M Y H:i')
    {
        if (!$date) {
            return '-';
        }

        return Carbon::parse($date)->setTimezone('Asia/Jakarta')->format($format) . ' WIB';
    }

    /**
     * Get human readable time difference in WIB
     */
    public static function diffForHumansWIB($date)
    {
        if (!$date) {
            return '-';
        }

        return Carbon::parse($date)->setTimezone('Asia/Jakarta')->diffForHumans();
    }
}