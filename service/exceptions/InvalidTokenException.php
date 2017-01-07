<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\service\exceptions;

/**
 * Invalid token exception is thrown when invalid token is used by user.
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class InvalidTokenException extends ServiceException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Invalid token';
    }
}