<?php

$time = time();

return [
    'test' => [
        'ip'                => '837ec5754f503cfaaee0929fd48974e7',
        'attempts'          => 10,
        'last_attempt_at'   => $time,
    ],
    'lockTime0' => [
        'ip'                => '837ec5754f503cfaaee0929fd48974e8',
        'attempts'          => 1,
        'last_attempt_at'   => $time,
    ],
];
