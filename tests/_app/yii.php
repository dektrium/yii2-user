#!/usr/bin/env php
<?php

require dirname(__DIR__) . '/_bootstrap.php';

defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));

$config = require(__DIR__ . '/config/console.php');

$exitCode = (new yii\console\Application($config))->run();
exit($exitCode);