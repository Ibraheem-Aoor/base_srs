<?php
namespace App\Enums;

enum ProgramCategoryEnum: string
{
    case MASTER = 'Master';
    case DOCTOR = 'Doctor';
    case BACHELOR = 'Bachelor';

    public static function getValues()
    {
        return array_column(self::cases(), 'value');
    }
}
