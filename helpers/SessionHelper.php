<?php


namespace dektrium\user\helpers;


use Yii;

/**
 * Session helper
 */
class SessionHelper
{
    /**
     * @return array
     */
    public function getCurrentHistoryData()
    {
        return [
            'user_id' => Yii::$app->user->id,
            'session_id' => Yii::$app->session->getId(),
            'user_agent' => Yii::$app->request->userAgent,
            'ip' => Yii::$app->request->userIP,
        ];
    }

    /**
     * @return array
     */
    public function getConditionCurrentHistoryData()
    {
        return [
            'user_id' => Yii::$app->user->id,
            'session_id' => Yii::$app->session->getId(),
            'user_agent' => Yii::$app->request->userAgent,
        ];
    }
}