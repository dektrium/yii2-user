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
 * UserSession model for tracking active user sessions across multiple devices.
 *
 * Database fields:
 * @property int $id
 * @property int $user_id
 * @property string $session_id Session identifier
 * @property string|null $ip_address IP address
 * @property string|null $user_agent Browser/device user agent
 * @property string|null $device_name Friendly device name (e.g., "Chrome on Windows")
 * @property string|null $location Geographic location (optional)
 * @property int $is_current Whether this is the current session
 * @property int $last_activity Timestamp of last activity
 * @property int $created_at Timestamp when session was created
 *
 * Relations:
 * @property User $user
 *
 * @author AlexeiKaDev
 */
class UserSession extends ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     * @return string
     */
    public static function tableName()
    {
        return '{{%user_session}}';
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [
            [['user_id', 'session_id'], 'required'],
            ['user_id', 'integer'],
            ['session_id', 'string', 'max' => 255],
            [['ip_address', 'location'], 'string', 'max' => 255],
            ['user_agent', 'string', 'max' => 500],
            ['device_name', 'string', 'max' => 100],
            ['is_current', 'boolean'],
            ['is_current', 'default', 'value' => 0],
            [['last_activity', 'created_at'], 'integer'],
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
     * Create or update session record for current user.
     *
     * @param int $userId User ID
     * @return UserSession|null
     */
    public static function createOrUpdate($userId)
    {
        $sessionId = Yii::$app->session->getId();

        // Find existing session or create new one
        $model = static::findOne(['session_id' => $sessionId]);

        if ($model === null) {
            $model = new static();
            $model->session_id = $sessionId;
            $model->user_id = $userId;
            $model->created_at = time();
        }

        $model->ip_address = Yii::$app->request->userIP;
        $model->user_agent = Yii::$app->request->userAgent;
        $model->device_name = self::parseDeviceName(Yii::$app->request->userAgent);
        $model->last_activity = time();
        $model->is_current = 1;

        // Mark all other sessions as not current
        static::updateAll(
            ['is_current' => 0],
            ['and', ['user_id' => $userId], ['!=', 'session_id', $sessionId]]
        );

        return $model->save() ? $model : null;
    }

    /**
     * Get all active sessions for a user.
     *
     * @param int $userId User ID
     * @param int $maxAge Maximum age in seconds (default: 30 days)
     * @return UserSession[]
     */
    public static function getUserSessions($userId, $maxAge = 2592000)
    {
        $timestamp = time() - $maxAge;

        return static::find()
            ->where(['user_id' => $userId])
            ->andWhere(['>=', 'last_activity', $timestamp])
            ->orderBy(['is_current' => SORT_DESC, 'last_activity' => SORT_DESC])
            ->all();
    }

    /**
     * Terminate a specific session.
     *
     * @param int $sessionId Session record ID
     * @param int $userId User ID (for security check)
     * @return bool Whether termination was successful
     */
    public static function terminateSession($sessionId, $userId)
    {
        $session = static::findOne(['id' => $sessionId, 'user_id' => $userId]);

        if ($session === null) {
            return false;
        }

        // If it's the current session, also destroy the Yii session
        if ($session->is_current) {
            Yii::$app->session->destroy();
        }

        return $session->delete() !== false;
    }

    /**
     * Terminate all other sessions except current.
     *
     * @param int $userId User ID
     * @return int Number of sessions terminated
     */
    public static function terminateOtherSessions($userId)
    {
        $currentSessionId = Yii::$app->session->getId();

        return static::deleteAll([
            'and',
            ['user_id' => $userId],
            ['!=', 'session_id', $currentSessionId]
        ]);
    }

    /**
     * Clean up expired sessions.
     *
     * @param int $maxAge Maximum age in seconds (default: 30 days)
     * @return int Number of sessions deleted
     */
    public static function cleanExpiredSessions($maxAge = 2592000)
    {
        $timestamp = time() - $maxAge;

        return static::deleteAll(['<', 'last_activity', $timestamp]);
    }

    /**
     * Update last activity timestamp for current session.
     *
     * @param int $userId User ID
     */
    public static function updateActivity($userId)
    {
        $sessionId = Yii::$app->session->getId();

        static::updateAll(
            ['last_activity' => time()],
            ['user_id' => $userId, 'session_id' => $sessionId]
        );
    }

    /**
     * Parse user agent string to get friendly device name.
     *
     * @param string|null $userAgent User agent string
     * @return string Friendly device name
     */
    protected static function parseDeviceName($userAgent)
    {
        if (empty($userAgent)) {
            return 'Unknown Device';
        }

        $browser = 'Unknown Browser';
        $os = 'Unknown OS';

        // Detect browser
        if (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($userAgent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $browser = 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            $browser = 'Edge';
        } elseif (strpos($userAgent, 'Opera') !== false || strpos($userAgent, 'OPR') !== false) {
            $browser = 'Opera';
        } elseif (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) {
            $browser = 'Internet Explorer';
        }

        // Detect OS
        if (strpos($userAgent, 'Windows NT 10.0') !== false) {
            $os = 'Windows 10';
        } elseif (strpos($userAgent, 'Windows NT 11.0') !== false) {
            $os = 'Windows 11';
        } elseif (strpos($userAgent, 'Windows') !== false) {
            $os = 'Windows';
        } elseif (strpos($userAgent, 'Mac OS X') !== false) {
            $os = 'macOS';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $os = 'Linux';
        } elseif (strpos($userAgent, 'Android') !== false) {
            $os = 'Android';
        } elseif (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            $os = 'iOS';
        }

        return $browser . ' on ' . $os;
    }

    /**
     * Get human-readable last activity time.
     *
     * @return string
     */
    public function getLastActivityFormatted()
    {
        $diff = time() - $this->last_activity;

        if ($diff < 60) {
            return Yii::t('user', 'Just now');
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return Yii::t('user', '{n} minute ago', ['n' => $minutes], $minutes);
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return Yii::t('user', '{n} hour ago', ['n' => $hours], $hours);
        } else {
            $days = floor($diff / 86400);
            return Yii::t('user', '{n} day ago', ['n' => $days], $days);
        }
    }

    /**
     * Check if session is currently active (within last 15 minutes).
     *
     * @return bool
     */
    public function isActive()
    {
        return (time() - $this->last_activity) < 900; // 15 minutes
    }
}
