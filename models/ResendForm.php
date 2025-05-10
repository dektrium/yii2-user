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

use AlexeiKaDev\Yii2User\Finder;
use AlexeiKaDev\Yii2User\Mailer;
use AlexeiKaDev\Yii2User\models\enums\TokenType;
use Yii;
use yii\base\Model;

/**
 * ResendForm gets user email address and if user with given email is registered it sends new confirmation message
 * to him in case he did not validate his email.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class ResendForm extends Model
{
    /** @var string User's email address. */
    public ?string $email = null;

    protected Mailer $mailer;
    protected Finder $finder;

    /**
     * ResendForm constructor.
     * @param Mailer $mailer
     * @param Finder $finder
     * @param array  $config
     */
    public function __construct(Mailer $mailer, Finder $finder, array $config = [])
    {
        $this->mailer = $mailer;
        $this->finder = $finder;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'email' => Yii::t('user', 'Email'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName(): string
    {
        return 'resend-form';
    }

    /**
     * Creates new confirmation token and sends it to the user.
     *
     * @return bool
     */
    public function resend(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->finder->findUserByEmail((string)$this->email);

        if ($user instanceof User && !$user->isConfirmed) {
            /** @var Token $token */
            $token = Yii::createObject([
                'class' => Token::class,
                'user_id' => $user->id,
                'type' => TokenType::CONFIRMATION,
            ]);
            $token->save(false);
            $this->mailer->sendConfirmationMessage($user, $token);
        }

        Yii::$app->session->setFlash(
            'info',
            Yii::t(
                'user',
                'A message has been sent to your email address. It contains a confirmation link that you must click to complete registration.'
            )
        );

        return true;
    }
}
