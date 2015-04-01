<?php

/* 
 * This file is part of the Dektrium project
 * 
 * (c) Dektrium project <http://github.com/dektrium>
 * 
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace dektrium\user\clients;

use yii\authclient\clients\Twitter as BaseTwitter;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Twitter extends BaseTwitter
{
    // current version of twitter api does not provide user's email, so we just
    // have a wrapper for base twitter client
}