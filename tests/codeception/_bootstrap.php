<?php

use AspectMock\Kernel;

if (getenv('TEST_ENVIRONMENT') === 'travis') {
    $vendor = __DIR__ . '/../../vendor';
} else {
    $vendor = __DIR__ . '/../../../../../vendor';
}

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_TEST_ENTRY_URL') or define('YII_TEST_ENTRY_URL', '/index.php');
defined('YII_TEST_ENTRY_FILE') or define('YII_TEST_ENTRY_FILE', __DIR__ . '/app/web/index.php');
defined('VENDOR_DIR') or define('VENDOR_DIR', $vendor);

require_once(VENDOR_DIR . '/autoload.php');

$kernel = Kernel::getInstance();
$kernel->init([
    'debug'        => true,
    'includePaths' => [__DIR__ . '/../../', VENDOR_DIR],
    'excludePaths' => [__DIR__],
    'cacheDir'     => __DIR__ . '/app/runtime/aop',
]);
$kernel->loadFile(VENDOR_DIR . '/yiisoft/yii2/Yii.php');

$_SERVER['SCRIPT_FILENAME'] = YII_TEST_ENTRY_FILE;
$_SERVER['SCRIPT_NAME']     = YII_TEST_ENTRY_URL;
$_SERVER['SERVER_NAME']     = 'localhost';

Yii::setAlias('@tests', dirname(__DIR__));
Yii::setAlias('@dektrium/user', realpath(__DIR__ . '..'));
