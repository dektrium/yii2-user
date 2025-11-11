<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\helpers;

use AlexeiKaDev\Yii2User\Module;
use Yii;

/**
 * Password helper.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Password
{
    /**
     * Wrapper for yii security helper method.
     *
     * @param string $password
     *
     * @return string
     */
    public static function hash($password)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('user');

        return Yii::$app->security->generatePasswordHash($password, $module->cost);
    }

    /**
     * Wrapper for yii security helper method.
     *
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public static function validate($password, $hash)
    {
        return Yii::$app->security->validatePassword($password, $hash);
    }

    /**
     * Generates user-friendly random password containing at least one lower case letter, one uppercase letter and one
     * digit. The remaining characters in the password are chosen at random from those three sets.
     * Uses cryptographically secure random_int() for PHP 7.2+.
     *
     * @see https://gist.github.com/tylerhall/521810
     *
     * @param int $length
     *
     * @return string
     * @throws \Exception
     */
    public static function generate($length)
    {
        $sets = [
            'abcdefghjkmnpqrstuvwxyz',
            'ABCDEFGHJKMNPQRSTUVWXYZ',
            '23456789',
        ];
        $all = '';
        $password = '';

        // Гарантированно добавляем по одному символу из каждого набора
        foreach ($sets as $set) {
            $password .= $set[random_int(0, strlen($set) - 1)];
            $all .= $set;
        }

        $allCharacters = str_split($all);
        $remainingLength = $length - count($sets);

        // Добавляем оставшиеся символы из объединенного набора
        if ($remainingLength > 0) { // Проверка, что длина >= кол-ва наборов
            for ($i = 0; $i < $remainingLength; $i++) {
                $password .= $allCharacters[random_int(0, count($allCharacters) - 1)];
            }
        }

        // Перемешиваем результат, чтобы гарантированные символы не были в начале
        // Используем криптографически безопасный метод перемешивания
        $passwordArray = str_split($password);
        $passwordLength = count($passwordArray);

        for ($i = $passwordLength - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            // Swap elements
            $temp = $passwordArray[$i];
            $passwordArray[$i] = $passwordArray[$j];
            $passwordArray[$j] = $temp;
        }

        return implode('', $passwordArray);
    }
}
