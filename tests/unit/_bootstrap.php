<?php
$_SERVER['SERVER_NAME'] = 'localhost/';
$_SERVER['SCRIPT_NAME'] = 'index.php';
$_SERVER['SCRIPT_FILENAME'] = 'index.php';

defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', false);

require_once(__DIR__ . '/../../../../vendor/autoload.php');
require_once(__DIR__ . '/../../../../vendor/yiisoft/yii2/yii/Yii.php');