<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\domain\exceptions;

use yii\base\Exception;

/**
 * Base exception class.
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class DomainException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Domain exception';
    }
}