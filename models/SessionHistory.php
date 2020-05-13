<?php

namespace dektrium\user\models;

use dektrium\user\Module;
use dektrium\user\traits\ModuleTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * SessionHistory ActiveRecord model.
 *
 * @property bool $isActive
 *
 * @property int $user_id
 * @property string $session_id
 * @property string $user_agent
 * @property string $ip
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 *
 * Dependencies:
 * @property-read Module $module
 */
class SessionHistory extends ActiveRecord
{
    use ModuleTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%session_history}}';
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('user', 'User ID'),
            'session_id' => Yii::t('user', 'Session ID'),
            'user_agent' => Yii::t('user', 'User agent'),
            'ip' => Yii::t('user', 'IP'),
            'created_at' => Yii::t('user', 'Created at'),
            'updated_at' => Yii::t('user', 'Last activity'),
        ];
    }

    /**
     * @return bool Whether the session is an active or not.
     */
    public function getIsActive()
    {
        return isset($this->session_id) && $this->updated_at + $this->getModule()->rememberFor > time();
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne($this->module->modelMap['User'], ['id' => 'user_id']);
    }

    /** @inheritdoc */
    public function beforeSave($insert)
    {
        if ($insert && empty($this->session_id)) {
            $this->setAttribute('session_id', Yii::$app->session->getId());
        }

        return parent::beforeSave($insert);
    }

    /** @inheritdoc */
    public static function primaryKey()
    {
        return ['user_id', 'session_id'];
    }
}
