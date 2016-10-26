<?php

/*
 * This file is part of the Dektrium project
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace dektrium\user\traits;

use yii\base\Model;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
trait AjaxValidationTrait
{
    /**
     * Performs ajax validation.
     *
     * @param Model $model
     *
     * @throws \yii\base\ExitException
     */
    protected function performAjaxValidation(Model $model)
    {
        if (\Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            \Yii::$app->response->data   = ActiveForm::validate($model);
            \Yii::$app->response->send();
            \Yii::$app->end();
        }
    }
}
