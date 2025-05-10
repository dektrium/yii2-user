<?php

declare(strict_types=1);

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
 * @property TokenType $type
 * @property-read string $url
 * @property-read bool $isExpired
 * @property-read User|null $user Relation defined by getUser(). Null if user does not exist.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Token extends ActiveRecord
{
    use ModuleTrait;

    /**
     * @var TokenType|null The type of the token.
     * This property will be automatically loaded by Yii as an int from the DB,
     * and should be cast to TokenType when accessed, if necessary.
     * When saving, Yii also expects an int.
     * For convenience, a getter/setter or afterFind/beforeValidate can be used for casting.
     */
    public ?TokenType $type = null;

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
     * Converts the integer type from the database to a TokenType enum instance after finding a record.
     */
    public function afterFind()
    {
        parent::afterFind();
        if (is_int($this->type) || (is_string($this->type) && ctype_digit($this->type))) {
            try {
                $this->type = TokenType::from((int)$this->type);
            } catch (\ValueError $e) {
                Yii::error(
                    "Invalid token type value '{$this->type}' from DB for token user_id: {$this->user_id}, code: {$this->code}. Error: {$e->getMessage()}",
                    __METHOD__
                );
                $this->type = null; // Set to null on error to prevent further issues
            }
        } elseif ($this->type !== null && !($this->type instanceof TokenType)) {
            Yii::warning(
                "Token type has an unexpected non-integer, non-enum type after find: " . get_debug_type($this->type),
                __METHOD__
            );
            $this->type = null;
        }
    }

    /**
     * Converts the TokenType enum instance to its integer value before validation and saving.
     * @return bool
     */
    public function beforeValidate(): bool
    {
        if ($this->type instanceof TokenType) {
            $this->type = $this->type->value;
        }
        // If $this->type is null here, 'required' rule will catch it.
        // If $this->type is an int not in enum range, 'in' rule will catch it.
        return parent::beforeValidate();
    }

    private function getInternalTokenType(): ?TokenType
    {
        if ($this->type instanceof TokenType) {
            return $this->type;
        }
        // This part might be redundant if afterFind and direct assignment always ensure TokenType or null
        if (is_int($this->type)) {
            try {
                return TokenType::from($this->type);
            } catch (\ValueError $e) {
                // Log if necessary, but primarily for internal consistency
                return null;
            }
        }
        return null;
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne($this->module->modelMap['User'], ['id' => 'user_id']);
    }

    /**
     * @return string The token URL for the specific token type.
     * @throws \RuntimeException If the token type is invalid.
     */
    public function getUrl(): string
    {
        $tokenType = $this->getInternalTokenType();

        if ($tokenType === null) {
            throw new \RuntimeException(
                'Cannot generate URL: Token type is unknown, invalid, or not set. User: ' . $this->user_id . ', Code: ' . $this->code
            );
        }

        $route = match ($tokenType) {
            TokenType::CONFIRMATION => '/user/registration/confirm',
            TokenType::RECOVERY => '/user/recovery/reset',
            TokenType::CONFIRM_NEW_EMAIL, TokenType::CONFIRM_OLD_EMAIL => '/user/settings/confirm',
            default => throw new \RuntimeException('Unknown token type: ' . ($tokenType instanceof TokenType ? $tokenType->name : $this->type)),
        };

        return Url::toRoute([$route, 'id' => $this->user_id, 'code' => $this->code], true);
    }

    /**
     * @return bool Whether token has expired.
     */
    public function getIsExpired(): bool
    {
        $tokenType = $this->getInternalTokenType();

        if ($tokenType === null) {
            Yii::warning(
                "Checking expiration for token with unknown, invalid or not set type for user {$this->user_id}, code: {$this->code}",
                __METHOD__
            );
            return true; // Safe default
        }

        $expirationTime = match ($tokenType) {
            TokenType::CONFIRMATION,
            TokenType::CONFIRM_NEW_EMAIL,
            TokenType::CONFIRM_OLD_EMAIL => $this->module->confirmWithin,
            TokenType::RECOVERY => $this->module->recoverWithin,
            default => null,
        };

        if ($expirationTime === null) {
            Yii::warning("Checking expiration for unknown token type: " . ($tokenType instanceof TokenType ? $tokenType->name : $this->type) . " for user {$this->user_id}", __METHOD__);

            return true;
        }

        return ($this->created_at + $expirationTime) < time();
    }

    /** @inheritdoc */
    public function beforeSave($insert): bool
    {
        // By this point, after beforeValidate, $this->type should be an integer (enum value) or null.
        // Validation rules (required, in range) should have ensured it's a valid int if not null.
        if ($this->type === null && $insert) { // Should be caught by 'required' validator
            Yii::error("Attempting to save a token with null type for user {$this->user_id}", __METHOD__);
            return false;
        }
        // No need to check is_int here if validators are correct, but as a safeguard:
        if (!is_int($this->type) && $this->type !== null) { // type can be null if !insert and it was null in DB
            Yii::error(
                "Token type is not an integer before save: " . get_debug_type($this->type) . " with value " . var_export($this->type, true) . " for user {$this->user_id}",
                __METHOD__
            );
            return false; 
        }

        if ($insert) {
            if ($this->type !== null) { // Ensure type is not null before using in deleteAll
                static::deleteAll(['user_id' => $this->user_id, 'type' => $this->type]);
            }
            $this->setAttribute('created_at', time());
            $this->setAttribute('code', Yii::$app->security->generateRandomString());
            // $this->type is already an int (or null), ActiveRecord will handle it for the 'type' attribute.
        }

        return parent::beforeSave($insert);
    }

    /** @inheritdoc */
    public static function tableName(): string
    {
        return '{{%token}}';
    }

    /** @inheritdoc */
    public static function primaryKey(): array
    {
        return ['user_id', 'code', 'type'];
    }
}
