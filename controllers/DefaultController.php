<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Default controller.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
            ],
        ];
    }

    public function actionIndex()
    {
        if (defined('YII_DEBUG') && YII_DEBUG) {
            return $this->render('index');
        } else {
            throw new NotFoundHttpException('Page not found');
        }
    }
}
