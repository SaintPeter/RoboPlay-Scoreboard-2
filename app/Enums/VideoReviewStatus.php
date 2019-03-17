<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class VideoReviewStatus extends Enum
{
    const Unreviewed = 0;
    const Reviewed = 1;
    const Disqualified = 2;
    const Passed = 3;

    /**
     * Get the description for an enum value
     *
     * @param  int  $value
     * @return string
     */
    public static function getDescription($value): string
    {
        switch ($value) {
            case self::Unreviewed:
                return 'Unreviewed';
	        case self::Reviewed:
	        	return 'Reviewed';
	        case self::Disqualified;
	            return 'Disqualified';
	        case self::Passed:
	        	return 'Passed';
            default:
                return self::getKey($value);
        }
    }
}
