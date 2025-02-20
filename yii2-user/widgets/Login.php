<?php

/*
 * This file is part of the DDMTechDev project.
 *
 * (c) DDMTechDev project <http://github.com/ddmtechdev>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace ddmtechdev\user\widgets;

use ddmtechdev\user\models\LoginForm;
use yii\base\Widget;

/**
 * Login for widget.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Login extends Widget
{
    /**
     * @var bool
     */
    public $validate = true;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('login', [
            'model' => \Yii::createObject(LoginForm::className()),
        ]);
    }
}
