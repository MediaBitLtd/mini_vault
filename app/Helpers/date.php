<?php

declare(strict_types=1);

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

if (!function_exists('datetime')) {
    /**
     * Date formatter [Y-m-d H:i:s]
     *
     * @param Carbon|null $date
     * @param bool $useTimezone
     * @return string|null
     */
    function datetime(?Carbon $date, bool $useTimezone = true): ?string
    {
        return $date?->copy()
            ->setTimezone($useTimezone ? Auth::user()?->timezone ?? 'Europe/London' : 'utc')
            ->format('Y-m-d H:i:s');

    }
}

if (!function_exists('ddate')) {
    /**
     * Date formatter [Y-m-d]
     *
     * @param Carbon|null $date
     * @param bool $useTimezone
     * @return string|null
     */
    function ddate(?Carbon $date, bool $useTimezone = true): ?string
    {
        return $date?->copy()
            ->setTimezone($useTimezone ? Auth::user()?->timezone ?? 'Europe/London' : 'utc')
            ->format('Y-m-d');

    }
}

if (!function_exists('dtime')) {
    /**
     * Time formatter [H:i:s]
     *
     * @param Carbon|string|null $date
     * @param bool $useTimezone
     * @param string $format
     * @return string|null
     */
    function dtime(Carbon|string|null $date, bool $useTimezone = true, string $format = 'H:i:s'): ?string
    {
        if (!$date) {
            return null;
        }

        if (!($date instanceof Carbon))
            $date = Carbon::parse($date, Auth::user()?->timezone ?? 'Europe/London');

        if ($format === 'pretty')
            $format = 'g:i a';

        return $date->copy()
            ->setTimezone($useTimezone ? Auth::user()?->timezone ?? 'Europe/London' : 'utc')
            ->format($format);
    }
}
