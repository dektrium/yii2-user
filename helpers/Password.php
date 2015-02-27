<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\helpers;

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
     * @param $password
     * @return string
     */
    public static function hash($password)
    {
        return \Yii::$app->security->generatePasswordHash($password, \Yii::$app->getModule('user')->cost);
    }

    /**
     * Wrapper for yii security helper method.
     *
     * @param $password
     * @param $hash
     * @return bool
     */
    public static function validate($password, $hash)
    {
        return \Yii::$app->security->validatePassword($password, $hash);
    }

    /**
     * Generates user-friendly random password containing at least one lower case letter, one uppercase letter and one
     * digit. The remaining characters in the password are chosen at random from those three sets.
     * @see https://gist.github.com/tylerhall/521810
     * @param $length
     * @return string
     */
    public static function generate($length)
    {
        $sets = [
            'abcdefghjkmnpqrstuvwxyz',
            'ABCDEFGHJKMNPQRSTUVWXYZ',
            '23456789'
        ];
        $all = '';
        $password = '';
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++) {
            $password .= $all[array_rand($all)];
        }

        $password = str_shuffle($password);

        return $password;
    }
}
