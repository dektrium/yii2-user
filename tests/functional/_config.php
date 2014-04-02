<?php

// set correct script paths
$_SERVER['SCRIPT_FILENAME'] = __DIR__.'/web/index.php';
$_SERVER['SCRIPT_NAME'] = __DIR__ . '/../_app/index.php';

return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../_app/config/web.php'),
    []
);
