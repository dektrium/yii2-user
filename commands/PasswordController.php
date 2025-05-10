<?php

declare(strict_types=1);

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\commands;

use AlexeiKaDev\Yii2User\Finder;
use AlexeiKaDev\Yii2User\Module;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Updates user's password.
 *
 * @property \AlexeiKaDev\Yii2User\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class PasswordController extends Controller
{
    protected Finder $finder;

    /**
     * @param string           $id
     * @param Module $module
     * @param Finder           $finder
     * @param array            $config
     */
    public function __construct(string $id, Module $module, Finder $finder, array $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /**
     * Updates user's password to given.
     *
     * @param string $search   Email or username
     * @param string $password New password
     */
    public function actionIndex(string $search, string $password): void
    {
        $user = $this->finder->findUserByUsernameOrEmail($search);

        if ($user === null) {
            $this->stdout(Yii::t('user', 'User is not found') . "\n", Console::FG_RED);
        } else {
            if ($user->resetPassword($password)) {
                $this->stdout(Yii::t('user', 'Password has been changed') . "\n", Console::FG_GREEN);
            } else {
                $this->stdout(Yii::t('user', 'Error occurred while changing password') . "\n", Console::FG_RED);
            }
        }
    }
}
