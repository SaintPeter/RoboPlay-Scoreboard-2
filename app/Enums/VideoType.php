<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class VideoType extends Enum
{
    const General = 1;
    const Custom = 2;
    const Compute = 3;

    /**
     * Get the description for an enum value
     *
     * @param  int  $value
     * @return string
     */
    public static function getDescription($value): string
    {
        switch ($value) {
            case self::General:
                return 'General';
                break;
            case self::Custom:
                return 'Custom';
                break;
            case self::Compute:
                return 'Compute';
                break;
            default:
                return self::getKey($value);
        }
    }
}
