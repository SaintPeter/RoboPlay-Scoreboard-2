<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class VideoCheckStatus extends Enum
{
    const Untested = 0;
    const Pass = 1;
    const Warnings = 2;
    const Fail = 99;

    const list = [
        self::Untested => 'Untested',
	    self::Pass => 'Pass',
	    self::Warnings => 'Warnings',
	    self::Fail => 'Fail'
    ];

    /**
     * Get the description for an enum value
     *
     * @param  int  $value
     * @return string
     */
    public static function getDescription(int $value): string
    {
        switch ($value) {
	        case self::Untested:
	        	return "Untested";
	        case self::Pass:
	        	return "Pass";
	        case self::Warnings:
	        	return "Warnings";
	        case self::Fail:
	        	return "Fail";
            default:
                return self::getKey($value);
        }
    }

	public static function toClasses(int $value): string
	{
		switch ($value) {
			case self::Untested:
				return "text-default";
			case self::Pass:
				return "text-success";
			case self::Warnings:
				return "text-warning";
			case self::Fail:
				return "text-danger";
			default:
				return "text-default";
		}
	}
}
