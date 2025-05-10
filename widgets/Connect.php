<?php

declare(strict_types=1);

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\widgets;

use AlexeiKaDev\Yii2User\models\Account;
use Yii;
use yii\authclient\ClientInterface;
use yii\authclient\widgets\AuthChoice;
use yii\authclient\widgets\AuthChoiceAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Connect extends AuthChoice
{
    /**
     * @var array<string, Account>|null An array of user's accounts, indexed by provider ID.
     */
    public ?array $accounts = null;

    /**
     * @inheritdoc
     */
    public $options = [];

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        AuthChoiceAsset::register(Yii::$app->getView());

        if ($this->popupMode) {
            Yii::$app->getView()->registerJs("\$('#" . $this->getId() . "').authchoice();");
        }
        $this->options['id'] = $this->getId();
        echo Html::beginTag('div', $this->options);
    }

    /**
     * @inheritdoc
     * @param ClientInterface $provider
     * @return string
     */
    public function createClientUrl($provider): string
    {
        if ($this->isConnected($provider)) {
            return Url::to(['/user/settings/disconnect', 'id' => $this->accounts[$provider->getId()]->id]);
        } else {
            return parent::createClientUrl($provider);
        }
    }

    /**
     * Checks if provider already connected to user.
     *
     * @param ClientInterface $provider
     *
     * @return bool
     */
    public function isConnected(ClientInterface $provider): bool
    {
        return $this->accounts !== null && isset($this->accounts[$provider->getId()]);
    }
}
