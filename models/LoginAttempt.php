<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\models;

/**
 * This is the model class for table "{{%login_attempt}}".
 *
 * @property integer $id
 * @property string $ip                 md5-sum of the ip
 * @property integer $attempts          Counter
 * @property integer $last_attempt_at   Unix timestamp (epoch time)
 *
 * @author jkmssoft
 */
class LoginAttempt extends \yii\db\ActiveRecord
{
    use \dektrium\user\traits\ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%login_attempt}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attempts', 'last_attempt_at'], 'integer'],
            [['ip'], 'string', 'max' => 32],
            [['ip'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('user', 'ID'),
            'ip' => \Yii::t('user', 'Ip'),
            'attempts' => \Yii::t('user', 'Attempts'),
            'last_attempt_at' => \Yii::t('user', 'Last Attempt At'),
        ];
    }

    /**
     * Get the remaining seconds for the login lock.
     * @return integer seconds
     */
    public function getLoginLockTime()
    {
        $lockTime = 0;
        if ($this->attempts > $this->module->numberOfAllowedInvalidLoginAttempts) {
            $lockTime = pow($this->attempts - $this->module->numberOfAllowedInvalidLoginAttempts, 2);
            $time = time();
            if ($time - $this->last_attempt_at <= $lockTime) {
                $lockTime = $lockTime - ($time - $this->last_attempt_at);
            } else {
                $lockTime = 0;
            }
        }
        return $lockTime;
    }

    /**
     * @inheritdoc
     * @return \dektrium\user\models\query\LoginAttemptQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \dektrium\user\models\query\LoginAttemptQuery(get_called_class());
    }
    
    /**
     * Delete old data.
     * @return boolean Delete result: true on success else false.
     */
    public static function purgeOld()
    {
        $seconds = time() - \Yii::$app->getModule('user')->secondsAfterLastInvalidLoginToResetCounter;
        return self::deleteAll(['<=', 'last_attempt_at', $seconds]) !== false;
    }
    
    /**
     * Remove invalid login attempt by ip.
     * @param string $ip
     * @return boolean Delete result: true on success else false.
     */
    public static function removeByIp($ip)
    {
        return self::deleteAll(['=', 'ip', $ip]) !== false;
    }
}
