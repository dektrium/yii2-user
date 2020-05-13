<?php

namespace dektrium\user\traits;

use dektrium\user\Module;
use yii\db\Connection;

/**
 * Trait ModuleTrait
 *
 * @property-read Module $module
 * @package dektrium\user\traits
 */
trait ModuleTrait
{
    /**
     * @return Module
     */
    public function getModule()
    {
        return \Yii::$app->getModule('user');
    }

    /**
     * @return Connection
     */
    public static function getDb()
    {
        return \Yii::$app->getModule('user')->getDb();
    }
}
