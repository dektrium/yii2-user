<?php

// set correct script paths
$_SERVER['SCRIPT_FILENAME'] = __DIR__.'/../../../../web/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';

return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../../config/web.php'),
    require(__DIR__ . '/../_config.php')
);
