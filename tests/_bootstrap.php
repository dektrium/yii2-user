<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');
define('VENDOR_DIR', __DIR__ . '/../../../vendor');

require_once(VENDOR_DIR . '/autoload.php');
require_once(VENDOR_DIR . '/yiisoft/yii2/Yii.php');

Yii::setAlias('@tests', __DIR__);
Yii::setAlias('@dektrium/user', realpath(__DIR__ . '..'));