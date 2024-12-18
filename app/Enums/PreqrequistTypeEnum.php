<?php
namespace App\Enums;

enum PreqrequistTypeEnum: string
{
    case PARALLEL = 'parallel';
    case PRIOR = 'prior';

    public static function getValues()
    {
        return array_column(self::cases(), 'value');
    }
}
