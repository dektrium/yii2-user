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

use dektrium\user\models\User;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
interface UserConfirmationInterface extends ServiceInterface
{
    /**
     * Confirms user without any checks.
     *
     * @param  User $user
     * @return bool
     */
    public function confirm(User $user);

    /**
     * Approves user without any checks.
     *
     * @param  User $user
     * @return bool
     */
    public function approve(User $user);

    /**
     * Attempts user confirmation with checking confirmation code.
     *
     * @param  User   $user
     * @param  string $code Confirmation code.
     * @return bool
     */
    public function attemptConfirmation(User $user = null, $code);

    /**
     * Resends confirmation message to user if user exists and is not confirmed.
     *
     * @param User|null $user
     */
    public function resendConfirmationMessage(User $user = null);
}