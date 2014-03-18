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
 * CreateController allows you to create user accounts.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class CreateController extends Controller
{
    /**
     * @var bool Whether user should confirm account.
     */
    public $confirmable = false;

    /**
     * Creates new user.
     *
     * @param string      $email
     * @param string      $username
     * @param null|string $password If null password will be generated automatically
     */
    public function actionIndex($email, $username, $password = null)
    {
        $this->module->trackable = false; // trackable should be disabled
        $this->module->confirmable = $this->confirmable;
        /** @var \dektrium\user\models\User $user */
        $user = $this->module->factory->createUser();
        $user->scenario = is_null($password) ? 'short_register' : 'register';
        $user->setAttributes([
            'email'    => $email,
            'username' => $username,
            'password' => $password
        ]);
        if (!$this->confirmable) {
            $user->confirmed_at = time();
        }
        if ($user->register()) {
            $this->stdout(\Yii::t('user', 'User has been created') . "!\n", Console::FG_GREEN);
        } else {
            $this->stdout(\Yii::t('user', 'Please fix following errors:') . "\n", Console::FG_RED);
            foreach ($user->errors as $errors) {
                foreach ($errors as $error) {
                    $this->stdout(" - ".$error."\n", Console::FG_RED);
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function options($id)
    {
        return array_merge(parent::options($id), ['confirmable']);
    }
}
