<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\events;

use yii\base\Event;
use yii\base\Model;

/**
 * @property Model $model
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class FormEvent extends Event
{
    /**
     * @var Model
     */
    private $_form;

    /**
     * @return Model
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * @param Model $form
     */
    public function setForm(Model $form)
    {
        $this->_form = $form;
    }
}