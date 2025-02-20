<?php

define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);

// Search for autoload, since performance is irrelevant and usability isn't!
$dir = __DIR__;
while (!file_exists($dir . '/vendor/autoload.php')) {
    if ($dir == dirname($dir)) {
        throw new \Exception('Failed to locate autoload.php');
    }
    $dir = dirname($dir);
}

$vendor = $dir . '/vendor';

define('VENDOR_DIR', $vendor);

require_once $vendor . '/autoload.php';
require $vendor . '/yiisoft/yii2/Yii.php';