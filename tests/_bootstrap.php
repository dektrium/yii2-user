<?php

define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);

// Search for autoload, since performance is irrelevant and usability isn't!
$dir = __DIR__; // Should be AlexeiKaDev/yii2-user/tests/

while (!file_exists($dir . '/vendor/autoload.php')) {
    if ($dir == dirname($dir)) { // Reached filesystem root
        $projectRootGuess = __DIR__ . '/../../../..'; 
        if (file_exists($projectRootGuess . '/vendor/autoload.php')) {
            $dir = $projectRootGuess;
            break;
        }
        throw new \Exception('Failed to locate autoload.php. CWD: ' . getcwd() . ' Initial __DIR__: ' . __DIR__);
    }
    $dir = dirname($dir);
}

$vendor = $dir . '/vendor';

require_once $vendor . '/autoload.php';
require_once $vendor . '/yiisoft/yii2/Yii.php';

if (!class_exists('Yii', false)) {
    throw new \Exception("Yii class not loaded by tests/_bootstrap.php itself!");
}

// All Yii loading and explicit checks are removed.
// This file will only define basic Yii environment constants.
