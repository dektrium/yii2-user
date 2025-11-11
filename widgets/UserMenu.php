<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\widgets;

use Yii;
use yii\base\Widget;
use yii\widgets\Menu;

/**
 * User menu widget.
 */
class UserMenu extends Widget
{
    /**
     * @var array<int, array<string, mixed>> The menu items for the yii\widgets\Menu.
     * Each item is an array configuring a single menu item.
     */
    public $items = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $networksVisible = count(Yii::$app->authClientCollection->getClients()) > 0; // Changed to getClients()

        // Default items, can be overridden by widget configuration
        if (empty($this->items)) {
            $this->items = [
                    ['label' => Yii::t('user', 'Profile'), 'url' => ['/user/settings/profile']],
                    ['label' => Yii::t('user', 'Account'), 'url' => ['/user/settings/account']],
                    [
                        'label' => Yii::t('user', 'Networks'),
                        'url' => ['/user/settings/networks'],
                        'visible' => $networksVisible
                    ],
                ];
        }
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function run()
    {
        return Menu::widget([
            'options' => [
                'class' => 'nav nav-pills flex-column',
            ],
            'items' => $this->items,
        ]);
    }
}
