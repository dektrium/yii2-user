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

use AlexeiKaDev\Yii2User\helpers\Password;
use AlexeiKaDev\Yii2User\traits\ModuleTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * BackupCode model for Two-Factor Authentication recovery codes.
 *
 * Recovery codes allow users to access their account if they lose their 2FA device.
 * Each code is single-use and should be stored securely (hashed like passwords).
 *
 * Best practices (2025):
 * - Generate 8-10 codes when enabling 2FA
 * - Codes should be 8-12 alphanumeric characters
 * - Store hashed (bcrypt) like passwords
 * - Allow regeneration anytime
 * - Warn user when codes are running low (< 3 remaining)
 *
 * Database fields:
 * @property int $id
 * @property int $user_id
 * @property string $code_hash Hashed backup code
 * @property int $used Whether the code has been used
 * @property int|null $used_at Timestamp when code was used
 * @property int $created_at Timestamp when code was created
 *
 * Relations:
 * @property User $user
 *
 * @author AlexeiKaDev
 */
class BackupCode extends ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     * @return string
     */
    public static function tableName()
    {
        return '{{%backup_code}}';
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [
            [['user_id', 'code_hash'], 'required'],
            ['user_id', 'integer'],
            ['code_hash', 'string', 'max' => 255],
            ['used', 'boolean'],
            ['used', 'default', 'value' => 0],
            [['used_at', 'created_at'], 'integer'],
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
     * Generate backup codes for a user.
     *
     * @param int $userId User ID
     * @param int $count Number of codes to generate (default: 10)
     * @return array Array of plain-text codes (must be shown to user once)
     */
    public static function generate($userId, $count = 10)
    {
        // Delete existing codes
        static::deleteAll(['user_id' => $userId]);

        $codes = [];

        for ($i = 0; $i < $count; $i++) {
            // Generate random 10-character alphanumeric code
            $code = self::generateRandomCode(10);
            $codes[] = $code;

            // Save hashed code
            $model = new static();
            $model->user_id = $userId;
            $model->code_hash = Password::hash($code);
            $model->save();
        }

        return $codes;
    }

    /**
     * Verify and use a backup code.
     *
     * @param int $userId User ID
     * @param string $code Plain-text code to verify
     * @return bool Whether code is valid and was successfully used
     */
    public static function verify($userId, $code)
    {
        // Get all unused codes for user
        $backupCodes = static::find()
            ->where(['user_id' => $userId, 'used' => 0])
            ->all();

        foreach ($backupCodes as $backupCode) {
            if (Password::validate($code, $backupCode->code_hash)) {
                // Mark code as used
                $backupCode->used = 1;
                $backupCode->used_at = time();
                $backupCode->save();

                return true;
            }
        }

        return false;
    }

    /**
     * Get remaining backup codes count for a user.
     *
     * @param int $userId User ID
     * @return int Number of unused codes
     */
    public static function getRemainingCount($userId)
    {
        return static::find()
            ->where(['user_id' => $userId, 'used' => 0])
            ->count();
    }

    /**
     * Check if user is running low on backup codes.
     *
     * @param int $userId User ID
     * @param int $threshold Warning threshold (default: 3)
     * @return bool Whether user has fewer codes than threshold
     */
    public static function isRunningLow($userId, $threshold = 3)
    {
        return self::getRemainingCount($userId) < $threshold;
    }

    /**
     * Get all backup codes for a user (including used status).
     *
     * @param int $userId User ID
     * @return BackupCode[]
     */
    public static function getUserCodes($userId)
    {
        return static::find()
            ->where(['user_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC, 'used' => SORT_ASC])
            ->all();
    }

    /**
     * Generate a random alphanumeric code.
     *
     * @param int $length Code length (default: 10)
     * @return string Random code
     */
    protected static function generateRandomCode($length = 10)
    {
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // No 0, O, 1, I to avoid confusion
        $code = '';

        $max = strlen($characters) - 1;

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, $max)];
        }

        // Format as XXXX-XXXX-XX for readability if length is 10
        if ($length === 10) {
            return substr($code, 0, 4) . '-' . substr($code, 4, 4) . '-' . substr($code, 8, 2);
        }

        return $code;
    }

    /**
     * Clean up old used codes (optional maintenance).
     *
     * @param int $months Number of months to retain used codes (default: 6)
     * @return int Number of codes deleted
     */
    public static function cleanOldUsedCodes($months = 6)
    {
        $timestamp = time() - ($months * 30 * 24 * 60 * 60);

        return static::deleteAll([
            'and',
            ['used' => 1],
            ['<', 'used_at', $timestamp]
        ]);
    }

    /**
     * Format code for display (add separators every 4 characters).
     *
     * @param string $code Plain-text code
     * @return string Formatted code
     */
    public static function formatCode($code)
    {
        // Remove existing separators
        $code = str_replace(['-', ' '], '', $code);

        // Add separator every 4 characters
        return implode('-', str_split($code, 4));
    }

    /**
     * Normalize code (remove separators and convert to uppercase).
     *
     * @param string $code Plain-text code
     * @return string Normalized code
     */
    public static function normalizeCode($code)
    {
        return strtoupper(str_replace(['-', ' '], '', $code));
    }

    /**
     * Get human-readable status.
     *
     * @return string Status label
     */
    public function getStatusLabel()
    {
        if ($this->used) {
            return Yii::t('user', 'Used on {date}', [
                'date' => date('Y-m-d H:i:s', $this->used_at)
            ]);
        }

        return Yii::t('user', 'Available');
    }
}
