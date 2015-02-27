<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace dektrium\user\widgets;

use dektrium\user\models\LoginForm;
use yii\base\Widget;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Login extends Widget
{
    /** @var bool */
    public $validate = true;

    /** @inheritdoc */
    public function run()
    {
        $model  = \Yii::createObject(LoginForm::className());
        $action = $this->validate ? null : ['/user/security/login'];

        if ($this->validate && $model->load(\Yii::$app->request->post()) && $model->login()) {
            return \Yii::$app->response->redirect(\Yii::$app->user->returnUrl);
        }

        return $this->render('login', [
            'model'  => $model,
            'action' => $action
        ]);
    }
}