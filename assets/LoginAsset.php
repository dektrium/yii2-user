<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\assets;

use yii\web\AssetBundle;

/**
 * LoginAsset.
 */
class LoginAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@dektrium/user/assets';
    /**
     * @inheritdoc
     */
    public $css = [
    ];
    /**
     * @inheritdoc
     */
    public $js = [
        'login.js',
    ];
    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
