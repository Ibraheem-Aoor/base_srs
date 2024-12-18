<?php
namespace App\Enums;

enum SubjectTypeEnum: string
{
    case COMPULSORY = 'Compulsory';
    case ELECTIVE = 'Elective';

    public static function getValues()
    {
        return array_column(self::cases(), 'value');
    }
}
