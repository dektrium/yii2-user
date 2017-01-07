<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\service\interfaces;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
interface ServiceInterface
{
    /**
     * Attaches needed event handlers.
     * @return void
     */
    public function attachEventHandlers();
}