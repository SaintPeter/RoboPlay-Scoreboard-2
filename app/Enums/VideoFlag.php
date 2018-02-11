<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class VideoFlag extends Enum
{
    // Global Defines for video flag statuses
    const Normal = 0;
    const Review = 1;
    const Disqualified = 2;

    /**
     * Get the description for an enum value
     *
     * @param  int  $value
     * @return string
     */
    public static function getDescription(int $value): string
    {
        switch ($value) {
            case self::Normal:
                return 'Normal';
                break;
            case self::Review:
                return 'Review';
                break;
            case self::Disqualified:
                return 'Disqualified';
                break;
            default:
                return self::getKey($value);
        }
    }
}
