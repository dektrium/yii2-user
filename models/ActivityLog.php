<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\models;

use AlexeiKaDev\Yii2User\traits\ModuleTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Activity Log model for tracking user actions (GDPR Article 30 compliance).
 *
 * Database fields:
 * @property int $id
 * @property int $user_id
 * @property string $action Action type (login, logout, password_change, profile_update, etc.)
 * @property string|null $ip_address IP address of the user
 * @property string|null $user_agent Browser/device user agent
 * @property string|null $location Geographic location (optional)
 * @property array|null $metadata Additional data in JSON format
 * @property int $created_at Timestamp when action occurred
 *
 * Relations:
 * @property User $user
 *
 * @author AlexeiKaDev
 */
class ActivityLog extends ActiveRecord
{
    use ModuleTrait;

    // Action types
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_REGISTER = 'register';
    const ACTION_PASSWORD_CHANGE = 'password_change';
    const ACTION_PASSWORD_RESET = 'password_reset';
    const ACTION_PROFILE_UPDATE = 'profile_update';
    const ACTION_EMAIL_CHANGE = 'email_change';
    const ACTION_ACCOUNT_DELETE = 'account_delete';
    const ACTION_2FA_ENABLED = '2fa_enabled';
    const ACTION_2FA_DISABLED = '2fa_disabled';
    const ACTION_SOCIAL_CONNECT = 'social_connect';
    const ACTION_SOCIAL_DISCONNECT = 'social_disconnect';
    const ACTION_FAILED_LOGIN = 'failed_login';
    const ACTION_ACCOUNT_BLOCKED = 'account_blocked';
    const ACTION_ACCOUNT_CONFIRMED = 'account_confirmed';

    /**
     * @inheritdoc
     * @return string
     */
    public static function tableName()
    {
        return '{{%activity_log}}';
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [
            [['user_id', 'action'], 'required'],
            ['user_id', 'integer'],
            ['action', 'string', 'max' => 50],
            [['ip_address', 'user_agent', 'location'], 'string', 'max' => 255],
            ['metadata', 'safe'],
            ['created_at', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne($this->module->modelMap['User'], ['id' => 'user_id']);
    }

    /**
     * Log an activity action.
     *
     * @param int $userId User ID
     * @param string $action Action type (use ACTION_* constants)
     * @param array $metadata Optional additional data
     * @return bool Whether the log was saved successfully
     */
    public static function log($userId, $action, $metadata = [])
    {
        $model = new static();
        $model->user_id = $userId;
        $model->action = $action;
        $model->ip_address = Yii::$app->request->userIP;
        $model->user_agent = Yii::$app->request->userAgent;
        $model->metadata = !empty($metadata) ? $metadata : null;

        return $model->save();
    }

    /**
     * Get activity logs for a specific user.
     *
     * @param int $userId User ID
     * @param int $limit Maximum number of records to return
     * @return ActivityLog[]
     */
    public static function getUserActivity($userId, $limit = 50)
    {
        return static::find()
            ->where(['user_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Get recent failed login attempts for a user.
     *
     * @param int $userId User ID
     * @param int $minutes Time window in minutes
     * @return int Number of failed attempts
     */
    public static function getFailedLoginAttempts($userId, $minutes = 15)
    {
        $timestamp = time() - ($minutes * 60);

        return static::find()
            ->where(['user_id' => $userId, 'action' => self::ACTION_FAILED_LOGIN])
            ->andWhere(['>=', 'created_at', $timestamp])
            ->count();
    }

    /**
     * Clean up old activity logs based on retention policy.
     * Default retention: 12 months (GDPR recommended).
     *
     * @param int $months Number of months to retain
     * @return int Number of records deleted
     */
    public static function cleanOldLogs($months = 12)
    {
        $timestamp = time() - ($months * 30 * 24 * 60 * 60);

        return static::deleteAll(['<', 'created_at', $timestamp]);
    }

    /**
     * Get human-readable action label.
     *
     * @return string
     */
    public function getActionLabel()
    {
        $labels = [
            self::ACTION_LOGIN => Yii::t('user', 'Logged in'),
            self::ACTION_LOGOUT => Yii::t('user', 'Logged out'),
            self::ACTION_REGISTER => Yii::t('user', 'Registered'),
            self::ACTION_PASSWORD_CHANGE => Yii::t('user', 'Changed password'),
            self::ACTION_PASSWORD_RESET => Yii::t('user', 'Reset password'),
            self::ACTION_PROFILE_UPDATE => Yii::t('user', 'Updated profile'),
            self::ACTION_EMAIL_CHANGE => Yii::t('user', 'Changed email'),
            self::ACTION_ACCOUNT_DELETE => Yii::t('user', 'Deleted account'),
            self::ACTION_2FA_ENABLED => Yii::t('user', 'Enabled 2FA'),
            self::ACTION_2FA_DISABLED => Yii::t('user', 'Disabled 2FA'),
            self::ACTION_SOCIAL_CONNECT => Yii::t('user', 'Connected social account'),
            self::ACTION_SOCIAL_DISCONNECT => Yii::t('user', 'Disconnected social account'),
            self::ACTION_FAILED_LOGIN => Yii::t('user', 'Failed login attempt'),
            self::ACTION_ACCOUNT_BLOCKED => Yii::t('user', 'Account blocked'),
            self::ACTION_ACCOUNT_CONFIRMED => Yii::t('user', 'Account confirmed'),
        ];

        return isset($labels[$this->action]) ? $labels[$this->action] : $this->action;
    }

    /**
     * Get formatted metadata.
     *
     * @return array
     */
    public function getMetadata()
    {
        if (is_string($this->metadata)) {
            return json_decode($this->metadata, true) ?: [];
        }

        return is_array($this->metadata) ? $this->metadata : [];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Convert metadata array to JSON string
            if (is_array($this->metadata)) {
                $this->metadata = json_encode($this->metadata);
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();

        // Convert JSON metadata back to array
        if (is_string($this->metadata)) {
            $this->metadata = json_decode($this->metadata, true);
        }
    }
}
