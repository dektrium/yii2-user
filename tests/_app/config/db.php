<?php

$db = [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('MYSQL_DATABASE'),
    'password' => getenv('MYSQL_PASSWORD'),
    'username' => getenv('MYSQL_USER'),
    'charset' => 'utf8',
//    'enableSavepoint' => false
];

return $db;