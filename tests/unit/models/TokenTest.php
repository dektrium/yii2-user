<?php


namespace AlexeiKaDev\Yii2User\Tests\Unit\Models;

use Codeception\Test\Unit;
use AlexeiKaDev\Yii2User\models\Token;
use AlexeiKaDev\Yii2User\models\enums\TokenType;
use UnitTester; // Это Actor класс для unit тестов

class TokenTest extends Unit
{
    protected $tester;

    public function testTokenTypeConversionOnNewRecord()
    {
        $token = new Token();
        $token->type = TokenType::CONFIRMATION;

        $this->assertInstanceOf(TokenType::class, $token->type);
        $this->assertSame(TokenType::CONFIRMATION, $token->type);

        // Имитируем вызов beforeValidate (в реальности он вызывается при $token->validate() или $token->save())
        // Для чистоты юнит-теста, вызовем его напрямую, если он public,
        // или протестируем через validate(), если он protected/private.
        // В данной модели beforeValidate() public.
        $token->beforeValidate();

        $this->assertIsInt($token->type);
        $this->assertSame(TokenType::CONFIRMATION->value, $token->type);
    }

    public function testTokenTypeConversionOnExistingRecord()
    {
        $token = new Token();
        // Имитируем, что тип загружен из БД как int
        $token->type = TokenType::RECOVERY->value;
        $this->assertIsInt($token->type);

        // Имитируем вызов afterFind (в реальности он вызывается после выборки из БД)
        // В данной модели afterFind() public.
        $token->afterFind();

        $this->assertInstanceOf(TokenType::class, $token->type);
        $this->assertSame(TokenType::RECOVERY, $token->type);
    }

    public function testIsExpiredDefaultsToTrueForUnknownType()
    {
        $token = new Token();
        // Устанавливаем заведомо неизвестный int тип, который не соответствует ни одному enum case
        // Предполагаем, что 999 не является валидным TokenType
        $token->type = 999; 
        $token->created_at = time() - 3600; // 1 час назад (не должно влиять)

        // Имитируем afterFind, чтобы тип попытался преобразоваться (он не должен найти enum case)
        // Если afterFind не вызывается, то this->type останется int
        // В getIsExpired() происходит $tokenType = is_int($this->type) ? TokenType::from($this->type) : $this->type;
        // Если TokenType::from() выбросит исключение для 999, тест должен это учитывать.
        // Судя по коду getIsExpired, он сам обрабатывает неизвестный тип и возвращает true.
        
        $this->assertTrue($token->getIsExpired(), "Token with unknown type should be considered expired by default.");
    }
} 