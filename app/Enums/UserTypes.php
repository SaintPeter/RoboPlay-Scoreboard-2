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
    const SuperAdmin = 256;

    /**
     * Get the description for an enum value
     *
     * @param  int  $value
     * @return string
     */
    public static function getDescription(int $value): string
    {
        switch ($value) {
            case self::NoRoles:
                return 'No Roles';
                break;
            case self::Guest:
                return 'Guest';
                break;
            case self::Teacher:
                return 'Teacher';
                break;
            case self::Judge:
                return 'Judge';
                break;
            case self::Admin:
                return 'Admin';
                break;
            case self::SuperAdmin:
                return 'SuperAdmin';
                break;
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
        if($value & self::SuperAdmin) $result[] = 'SuperAdmin';

        return $result;
    }
}
