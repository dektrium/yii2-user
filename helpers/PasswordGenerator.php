<?php

namespace dektrium\user\helpers;
use yii\base\Object;

/**
 * Password generator.
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class PasswordGenerator extends Object
{
    /**
     * @var array
     */
    public $sets = [
        'abcdefghjkmnpqrstuvwxyz',
        'ABCDEFGHJKMNPQRSTUVWXYZ',
        '23456789',
    ];

    /**
     * @var int
     */
    public $length = 8;

    /**
     * Generates new random password.
     * @param  null|int $length
     * @return string
     */
    public function generate($length = null)
    {
        if (!$length) {
            $length = $this->length;
        }

        $password = '';
        $chars = implode('', $this->sets);

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $password;
    }
}