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

use dektrium\user\Finder;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Deletes a user.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class DeleteController extends Controller
{
    /** @var Finder */
    protected $finder;

    /**
     * @param string $id
     * @param \yii\base\Module $module
     * @param Finder $finder
     * @param array $config
     */
    public function __construct($id, $module, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /**
     * Deletes a user.
     *
     * @param string $search Email or username
     */
    public function actionIndex($search)
    {
        if ($this->confirm(\Yii::t('user', 'Are you sure? Deleted user can not be restored'))) {
            $user = $this->finder->findUserByUsernameOrEmail($search);
            if ($user === null) {
                $this->stdout(\Yii::t('user', 'User is not found') . "\n", Console::FG_RED);
            } else {
                if ($user->delete()) {
                    $this->stdout(\Yii::t('user', 'User has been deleted') . "\n", Console::FG_GREEN);
                } else {
                    $this->stdout(\Yii::t('user', 'Error occurred while deleting user') . "\n", Console::FG_RED);
                }
            }
        }
    }
}
