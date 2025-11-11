<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\models\enums;

/**
 * Token type constants (replaces enum for PHP 7.2+ compatibility).
 *
 * @author AlexeiKaDev
 */
class TokenType
{
    const CONFIRMATION = 0;
    const RECOVERY = 1;
    const CONFIRM_NEW_EMAIL = 2;
    const CONFIRM_OLD_EMAIL = 3;

    /**
     * Returns all token type values.
     * @return int[]
     */
    public static function values()
    {
        return [
            self::CONFIRMATION,
            self::RECOVERY,
            self::CONFIRM_NEW_EMAIL,
            self::CONFIRM_OLD_EMAIL,
        ];
    }
}
