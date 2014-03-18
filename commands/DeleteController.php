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
 * DeleteController allows you to delete user accounts.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class DeleteController extends Controller
{
    /**
     * Deletes a user by email.
     *
     * @param string $email
     */
    public function actionIndex($email)
    {
        if ($this->confirm(\Yii::t('user', 'Are you sure? Deleted user can not be restored!'))) {
            $query = $this->module->factory->createUserQuery();
            /** @var \dektrium\user\models\User $user */
            $user = $query->where(['email' => $email])->one();
            if ($user === null) {
                $this->stdout(\Yii::t('user', 'User is not found!') . "\n", Console::FG_RED);
            } else {
                if ($user->delete()) {
                    $this->stdout("\n", Console::FG_GREEN);
                } else {
                    $this->stdout(\Yii::t('user', 'Error occurred while deleting user!') . "\n", Console::FG_RED);
                }
            }
        }
    }
}
