<?php

$time = time();

return [
    'user' => [
        'username'      => 'user',
        'email'         => 'user@example.com',
        'password_hash' => '$2y$13$qY.ImaYBppt66qez6B31QO92jc5DYVRzo5NxM1ivItkW74WsSG6Ui',
        'auth_key'      => '39HU0m5lpjWtqstFVGFjj6lFb7UZDeRq',
        'created_at'    => $time,
        'updated_at'    => $time,
        'confirmed_at'  => $time,
    ],
    'unconfirmed' => [
        'username'      => 'joe',
        'email'         => 'joe@example.com',
        'password_hash' => '$2y$13$CIH1LSMPzU9xDCywt3QO8uovAu2axp8hwuXVa72oI.1G/USsGyMBS',
        'auth_key'      => 'mhh1A6KfqQLmHP-MiWN0WB0M90Q2u5OE',
        'created_at'    => $time,
        'updated_at'    => $time,
    ],
    'unconfirmed_with_expired_token' => [
        'username'      => 'john',
        'email'         => 'john@example.com',
        'password_hash' => '$2y$13$qY.ImaYBppt66qez6B31QO92jc5DYVRzo5NxM1ivItkW74WsSG6Ui',
        'auth_key'      => 'h6OS9csJbZEOW59ZILmJxU6bCiqVno9A',
        'created_at'    => $time - 86401,
        'updated_at'    => $time - 86401,
    ],
    'blocked' => [
        'username'      => 'steven',
        'email'         => 'steven@example.com',
        'password_hash' => '$2y$13$qY.ImaYBppt66qez6B31QO92jc5DYVRzo5NxM1ivItkW74WsSG6Ui',
        'auth_key'      => 'TnXTrtLdj-YJBlG2A6jFHJreKgbsLYCa',
        'created_at'    => $time,
        'updated_at'    => $time,
        'blocked_at'    => $time,
        'confirmed_at'  => $time,
    ],
    'user_with_expired_recovery_token' => [
        'username'      => 'andrew',
        'email'         => 'andrew@example.com',
        'password_hash' => '$2y$13$qY.ImaYBppt66qez6B31QO92jc5DYVRzo5NxM1ivItkW74WsSG6Ui',
        'auth_key'      => 'qxYa315rqRgCOjYGk82GFHMEAV3T82AX',
        'created_at'    => $time - 21601,
        'updated_at'    => $time - 21601,
        'confirmed_at'  => $time - 21601,
    ],
    'user_with_recovery_token' => [
        'username'      => 'alex',
        'email'         => 'alex@example.com',
        'password_hash' => '$2y$13$qY.ImaYBppt66qez6B31QO92jc5DYVRzo5NxM1ivItkW74WsSG6Ui',
        'auth_key'      => 'zQh1A65We0AmHPOMiWN0WB0M90Q24ziU',
        'created_at'    => $time,
        'updated_at'    => $time,
        'confirmed_at'  => $time,
    ],
];
