<?php

$time = time();

return [
    'confirmation' => [
        'user_id' => 2,
        'code' => 'NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6',
        'type' => \dektrium\user\models\Token::TYPE_CONFIRMATION,
        'created_at' => $time
    ],
    'expired_confirmation' => [
        'user_id' => 3,
        'code' => 'qxYa315rqRgCOjYGk82GFHMEAV3T82AX',
        'type' => \dektrium\user\models\Token::TYPE_CONFIRMATION,
        'created_at' => $time - 86401
    ]
];