<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\helpers;

/**
 * @property \dektrium\user\Module $module
 */
trait ModuleTrait
{
    /**
     * @var null|\dektrium\user\Module
     */
    private $_module;

    /**
     * @return null|\dektrium\user\Module
     */
    protected function getModule()
    {
        if ($this->_module == null) {
            $this->_module = \Yii::$app->getModule('user');
        }

        return $this->_module;
    }
}