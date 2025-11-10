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

use AlexeiKaDev\Yii2User\Module;
use AlexeiKaDev\Yii2User\traits\ModuleTrait;
use DateTime;
use DateTimeZone;
use Exception;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "profile".
 *
 * @property int $user_id
 * @property string|null $name
 * @property string|null $public_email
 * @property string|null $gravatar_email
 * @property string|null $gravatar_id
 * @property string|null $location
 * @property string|null $website
 * @property string|null $bio
 * @property string|null $timezone
 * @property User $user User relation defined by getUser().
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Profile extends ActiveRecord
{
    use ModuleTrait;

    /** @var Module The user module instance */
    protected $module;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init(); // Call parent::init() at the beginning
        /** @var Module $userModule */
        $userModule = Yii::$app->getModule('user');
        $this->module = $userModule;
    }

    /**
     * Returns avatar url.
     * @param int $size Size in pixels.
     * @return string
     */
    public function getAvatarUrl($size = 200)
    {
        // Используем coalesce оператор для краткости
        $emailToHash = strtolower(trim((string)($this->gravatar_email ?? $this->public_email ?? $this->user->email ?? '')));
        $gravatarId = $this->gravatar_id ?: md5($emailToHash);

        // Если gravatarId все еще пуст (например, email не был найден или пуст)
        if (empty($gravatarId)) {
            // Можно вернуть дефолтный аватар или использовать другую логику
            // md5('') вернет 'd41d8cd98f00b204e9800998ecf8427e', что тоже даст Gravatar
            $gravatarId = md5('');
        }

        return '//gravatar.com/avatar/' . $gravatarId . '?s=' . $size . '&d=mp'; // d=mp для дефолтного аватара
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne($this->module->modelMap['User'], ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [
            ['bio', 'string'],
            ['timezone', 'validateTimeZone'],
            ['public_email', 'email'],
            ['gravatar_email', 'email'],
            ['website', 'url'],
            ['name', 'string', 'max' => 255],
            ['public_email', 'string', 'max' => 255],
            ['gravatar_email', 'string', 'max' => 255],
            ['location', 'string', 'max' => 255],
            ['website', 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('user', 'Name'),
            'public_email' => Yii::t('user', 'Email (public)'),
            'gravatar_email' => Yii::t('user', 'Gravatar email'),
            'location' => Yii::t('user', 'Location'),
            'website' => Yii::t('user', 'Website'),
            'bio' => Yii::t('user', 'Bio'),
            'timezone' => Yii::t('user', 'Time zone'),
        ];
    }

    /**
     * Validates the timezone attribute.
     * Adds an error when the specified time zone doesn't exist.
     * @param string $attribute the attribute being validated
     * @param array|null $params values for the placeholders in the error message
     */
    public function validateTimeZone($attribute, $params = null)
    {
        /** @var string|null $value */
        $value = $this->$attribute; // Доступ к свойству напрямую

        if ($value === null || $value === '') {
            return;
        }

        if (!in_array($value, DateTimeZone::listIdentifiers(), true)) {
            $this->addError($attribute, Yii::t('user', 'Time zone is not valid'));
        }
    }

    /**
     * Get the user's time zone.
     * Defaults to the application timezone if not specified by the user.
     * @return DateTimeZone
     */
    public function getTimeZone()
    {
        try {
            if (!empty($this->timezone)) {
                return new DateTimeZone($this->timezone);
            }
        } catch ($e) { // Используем импортированный Exception
            // Log error if needed:
            Yii::warning(
                "Invalid timezone '{$this->timezone}' for user {$this->user_id}: " . $e->getMessage(),
                __METHOD__
            );
        }

        // Возвращаем таймзону приложения как fallback
        return new DateTimeZone(Yii::$app->timeZone);
    }

    /**
     * Set the user's time zone.
     * @param DateTimeZone $timeZone the timezone to save to the user's profile
     */
    public function setTimeZone($timeZone)
    {
        $this->setAttribute('timezone', $timeZone->getName());
    }

    /**
     * Converts DateTime to user's local time
     * @param DateTime|null $dateTime the datetime to convert, defaults to current time
     * @return DateTime
     */
    public function toLocalTime($dateTime = null)
    {
        $dateTime = $dateTime ?? new DateTime();

        return $dateTime->setTimezone($this->getTimeZone());
    }

    /**
     * @inheritdoc
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $gravatarEmail = $this->getAttribute('gravatar_email');
        $isGravatarEmailChanged = $this->isAttributeChanged('gravatar_email');

        if ($isGravatarEmailChanged || ($insert && !empty($gravatarEmail))) {
            $emailToHash = strtolower(trim((string)$gravatarEmail));
            $this->setAttribute('gravatar_id', !empty($emailToHash) ? md5($emailToHash) : null);
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     * @return string
     */
    public static function tableName()
    {
        return '{{%profile}}';
    }
}
