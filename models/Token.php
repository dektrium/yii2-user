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

use AlexeiKaDev\Yii2User\models\enums\TokenType;
use AlexeiKaDev\Yii2User\traits\ModuleTrait;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * Token Active Record model.
 *
 * @property int $user_id
 * @property string $code
 * @property int $created_at
 * @property int $type Token type (stored as integer)
 * @property-read string $url
 * @property-read bool $isExpired
 * @property-read User|null $user Relation defined by getUser(). Null if user does not exist.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Token extends ActiveRecord
{
    use ModuleTrait;

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['user_id', 'type'], 'required'],
            ['user_id', 'integer'],
            ['type', 'integer'],
            ['type', 'in', 'range' => TokenType::values()],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne($this->module->modelMap['User'], ['id' => 'user_id']);
    }

    /**
     * @return string The token URL for the specific token type.
     * @throws \RuntimeException If the token type is invalid.
     */
    public function getUrl()
    {
        $route = null;

        switch ($this->type) {
            case TokenType::CONFIRMATION:
                $route = '/user/registration/confirm';
                break;
            case TokenType::RECOVERY:
                $route = '/user/recovery/reset';
                break;
            case TokenType::CONFIRM_NEW_EMAIL:
            case TokenType::CONFIRM_OLD_EMAIL:
                $route = '/user/settings/confirm';
                break;
            default:
                throw new \RuntimeException('Unknown token type: ' . $this->type);
        }

        return Url::toRoute([$route, 'id' => $this->user_id, 'code' => $this->code], true);
    }

    /**
     * @return bool Whether token has expired.
     */
    public function getIsExpired()
    {
        $expirationTime = null;

        switch ($this->type) {
            case TokenType::CONFIRMATION:
            case TokenType::CONFIRM_NEW_EMAIL:
            case TokenType::CONFIRM_OLD_EMAIL:
                $expirationTime = $this->module->confirmWithin;
                break;
            case TokenType::RECOVERY:
                $expirationTime = $this->module->recoverWithin;
                break;
            default:
                Yii::warning("Checking expiration for unknown token type: " . $this->type . " for user {$this->user_id}", __METHOD__);
                return true;
        }

        if ($expirationTime === null) {
            return true;
        }

        return ($this->created_at + $expirationTime) < time();
    }

    /** @inheritdoc */
    public function beforeSave($insert)
    {
        if ($this->type === null && $insert) {
            Yii::error("Attempting to save a token with null type for user {$this->user_id}", __METHOD__);
            return false;
        }

        if (!is_int($this->type) && $this->type !== null) {
            Yii::error(
                "Token type is not an integer before save: " . gettype($this->type) . " with value " . var_export($this->type, true) . " for user {$this->user_id}",
                __METHOD__
            );
            return false;
        }

        if ($insert) {
            if ($this->type !== null) {
                static::deleteAll(['user_id' => $this->user_id, 'type' => $this->type]);
            }
            $this->setAttribute('created_at', time());
            $this->setAttribute('code', Yii::$app->security->generateRandomString());
        }

        return parent::beforeSave($insert);
    }

    /** @inheritdoc */
    public static function tableName()
    {
        return '{{%token}}';
    }

    /** @inheritdoc */
    public static function primaryKey()
    {
        return ['user_id', 'code', 'type'];
    }
}
