<?php

namespace tests\_fixtures;

class ProfileFixture extends ActiveFixture
{
    public $modelClass = 'dektrium\user\models\Profile';

    public $depends = [
        'tests\_fixtures\UserFixture'
    ];


}
