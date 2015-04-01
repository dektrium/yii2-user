<?php

if (getenv('TEST_ENVIRONMENT') === 'travis') {
    $vendor = __DIR__ . '/../../vendor';
} else {
    $vendor = __DIR__ . '/../../../../../vendor';
}

defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_TEST_ENTRY_URL') or define('YII_TEST_ENTRY_URL', '/index.php');
defined('YII_TEST_ENTRY_FILE') or define('YII_TEST_ENTRY_FILE', __DIR__ . '/_app/web/index.php');
defined('VENDOR_DIR') or define('VENDOR_DIR', $vendor);

require_once(VENDOR_DIR . '/autoload.php');