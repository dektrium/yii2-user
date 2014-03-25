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
class ConfirmController extends Controller
{
    /**
     * Confirms a user.
     *
     * @param string $search Email or username
     */
    public function actionIndex($search)
    {
        $user = $this->module->manager->findUserByUsernameOrEmail($search);
        if ($user === null) {
            $this->stdout(\Yii::t('user', 'User is not found') . "\n", Console::FG_RED);
        } else {
            if ($user->confirm(false)) {
                $this->stdout(\Yii::t('user', 'User has been confirmed') . "\n", Console::FG_GREEN);
            } else {
                $this->stdout(\Yii::t('user', 'Error occurred while confirming user') . "\n", Console::FG_RED);
            }
        }
    }
}
