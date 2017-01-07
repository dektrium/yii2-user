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
 * Invalid token exception is thrown when invalid user is provided.
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class InvalidUserException extends ServiceException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Invalid user';
    }
}