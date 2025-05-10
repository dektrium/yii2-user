<?php

declare(strict_types=1);

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
    public static function hash(string $password): string
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
    public static function validate(string $password, string $hash): bool
    {
        return Yii::$app->security->validatePassword($password, $hash);
    }

    /**
     * Generates user-friendly random password containing at least one lower case letter, one uppercase letter and one
     * digit. The remaining characters in the password are chosen at random from those three sets.
     *
     * @see https://gist.github.com/tylerhall/521810
     *
     * @param int $length
     *
     * @return string
     */
    public static function generate(int $length): string
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
            $password .= $set[array_rand(str_split($set))]; // Оригинальный метод Dektrium
            // Альтернатива для PHP 7+: $password .= $set[random_int(0, mb_strlen($set) - 1)];
            $all .= $set;
        }

        $allCharacters = str_split($all);
        $remainingLength = $length - count($sets);

        // Добавляем оставшиеся символы из объединенного набора
        if ($remainingLength > 0) { // Проверка, что длина >= кол-ва наборов
            for ($i = 0; $i < $remainingLength; $i++) {
                $password .= $allCharacters[array_rand($allCharacters)];
                // Альтернатива для PHP 7+: $password .= $allCharacters[random_int(0, count($allCharacters) - 1)];
            }
        }

        // Перемешиваем результат, чтобы гарантированные символы не были в начале
        return str_shuffle($password);
    }
}
