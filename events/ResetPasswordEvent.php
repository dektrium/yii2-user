<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\events;

use AlexeiKaDev\Yii2User\models\RecoveryForm;
use AlexeiKaDev\Yii2User\models\Token;
use yii\base\Event;

/**
 * @property Token|null        $token
 * @property RecoveryForm|null $form
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class ResetPasswordEvent extends Event
{
    /**
     * @var RecoveryForm|null
     */
    private $_form = null;

    /**
     * @var Token|null
     */
    private $_token = null;

    /**
     * @return Token|null
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * @param Token|null $token
     */
    public function setToken($token = null)
    {
        $this->_token = $token;
    }

    /**
     * @return RecoveryForm|null
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * @param RecoveryForm|null $form
     */
    public function setForm($form = null)
    {
        $this->_form = $form;
    }
}
