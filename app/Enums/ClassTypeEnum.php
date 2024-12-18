<?php
namespace App\Enums;

enum ClassTypeEnum: string
{
    case THEORY = 'Theory';
    case SEMI_THEORY = 'Semi-Theory';

    public static function getValues()
    {
        return array_column(self::cases(), 'value');
    }
}
