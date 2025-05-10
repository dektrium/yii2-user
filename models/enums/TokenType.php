<?php

declare(strict_types=1);

namespace AlexeiKaDev\Yii2User\models\enums;

enum TokenType: int
{
    case CONFIRMATION = 0;
    case RECOVERY = 1;
    case CONFIRM_NEW_EMAIL = 2;
    case CONFIRM_OLD_EMAIL = 3;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
