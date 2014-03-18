<?php

/*
* This file is part of the Dektrium project.
*
* (c) Dektrium project <http://github.com/dektrium/>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace dektrium\user\commands;

use yii\console\Controller;
use yii\helpers\Console;

/**
 * PasswordController allows you to change user's passwords.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class PasswordController extends Controller
{
    /**
     * Changes user's password to given.
     *
     * @param string $email
     * @param string $password
     */
    public function actionIndex($email, $password)
    {
        $query = $this->module->factory->createUserQuery();
        /** @var \dektrium\user\models\User $user */
        $user = $query->where(['email' => $email])->one();
        if ($user === null) {
            $this->stdout(\Yii::t('user', 'User is not found!') . "\n", Console::FG_RED);
        } else {
            if ($user->resetPassword($password)) {
                $this->stdout(\Yii::t('user', 'Password has been changed!') . "\n", Console::FG_GREEN);
            } else {
                $this->stdout(\Yii::t('user', 'Error occurred while changing password!') . "\n", Console::FG_RED);
            }
        }
    }
}
