<?php

namespace AlexeiKaDev\Yii2User\traits;

use AlexeiKaDev\Yii2User\Module;
use Yii;

/**
 * Trait ModuleTrait
 *
 * @property-read Module $module
 * @package AlexeiKaDev\Yii2User\traits
 */
trait ModuleTrait
{
    /**
     * @return Module
     */
    public function getModule(): Module
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('user');

        return $module;
    }

    /**
     * @return \yii\db\Connection
     */
    public static function getDb(): \yii\db\Connection
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('user');

        return $module->getDb();
    }
}
