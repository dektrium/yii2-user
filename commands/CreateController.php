<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace dektrium\user\commands;

use yii\console\Controller;
use yii\helpers\Console;

/**
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class CreateController extends Controller
{
    /**
     * @var bool Whether user should confirm account.
     */
    public $unconfirmed = false;

    /**
     * Creates new user account. If password is not set it will be generated automatically. After creation email
     * message contains username and password will be sent to user.
     *
     * @param string      $email
     * @param string      $username
     * @param null|string $password If null password will be generated automatically
     */
    public function actionIndex($email, $username, $password = null)
    {
        $this->module->confirmable = $this->unconfirmed;
        $user = $this->module->manager->createUser(['scenario' => 'create']);
        $user->setAttributes([
            'email'    => $email,
            'username' => $username,
            'password' => $password
        ]);
        if ($user->create()) {
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
        return array_merge(parent::options($id), ['unconfirmed']);
    }
}
