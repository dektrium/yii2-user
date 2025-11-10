<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\widgets;

use AlexeiKaDev\Yii2User\models\LoginForm;
use Yii;
use yii\base\Widget;

/**
 * Login for widget.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Login extends Widget
{
    /**
     * @var bool Whether to register related assets. Defaults to true.
     * This property is not directly used in the provided run() method,
     * but kept for compatibility or future use.
     */
    public $validate = true;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('login', [
            'model' => Yii::createObject(LoginForm::class),
        ]);
    }
}
