<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserTypes extends Enum
{
    const NoRoles = 0;
    const Guest = 1;
    const Teacher = 2;
    const Judge = 4;
    const Admin = 8;
    const VideoReviewer = 16;
    const SuperAdmin = 256;
    
    public static $RoleList = [
        self::NoRoles    =>  'No Roles',
		self::Guest      =>  'Guest',
		self::Teacher    =>  'Teacher',
	    self::VideoReviewer =>  'Video Reviewer',
		self::Judge      =>  'Judge',
		self::Admin      =>  'Admin',
		self::SuperAdmin =>  'SuperAdmin'
    ];

    /**
     * Get the description for an enum value
     *
     * @param  int  $value
     * @return string
     */
    public static function getDescription($value): string
    {
        switch ($value) {
	        case self::NoRoles:
		        return 'No Roles';
	        case self::Guest:
		        return 'Guest';
	        case self::Teacher:
		        return 'Teacher';
	        case self::Judge:
		        return 'Judge';
	        case self::Admin:
		        return 'Admin';
	        case self::VideoReviewer:
		        return 'Video Reviewer';
	        case self::SuperAdmin:
		        return 'SuperAdmin';
	        default:
		        return self::getKey($value);
        }
    }

    /**
     * Return all descriptions in this bit field
     *
     * @param int $value Bit-field with one or more Roles
     * @return array List of Descriptions
     */
    public static function getAllDescriptions(int $value): array
    {
        $result = [];
        if(!$value) return ['No Roles'];
        if($value & self::Guest) $result[] = 'Guest';
        if($value & self::Teacher) $result[] = 'Teacher';
        if($value & self::Judge) $result[] = 'Judge';
        if($value & self::Admin) $result[] = 'Admin';
        if($value & self::VideoReviewer) $result[] = 'Video Reviewer';
        if($value & self::SuperAdmin) $result[] = 'SuperAdmin';

        return $result;
    }
}
