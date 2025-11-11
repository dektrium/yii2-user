<?php

/*
 * This file is part of the AlexeiKaDev yii2-user project.
 *
 * (c) AlexeiKaDev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\interfaces;

/**
 * Two-Factor Authentication Interface
 *
 * Implement this interface in your User model to enable 2FA support.
 * You'll need to install a 2FA module like hiqdev/yii2-mfa or vxm/yii2-mfa.
 *
 * Example implementation:
 * ```php
 * class User extends ActiveRecord implements TwoFactorInterface
 * {
 *     public function getIsTwoFactorEnabled()
 *     {
 *         return (bool)$this->two_factor_enabled;
 *     }
 *
 *     public function getTwoFactorSecret()
 *     {
 *         return $this->two_factor_secret;
 *     }
 *
 *     public function setTwoFactorSecret($secret)
 *     {
 *         $this->two_factor_secret = $secret;
 *         return $this->save(false, ['two_factor_secret']);
 *     }
 *
 *     public function enableTwoFactor()
 *     {
 *         $this->two_factor_enabled = 1;
 *         return $this->save(false, ['two_factor_enabled']);
 *     }
 *
 *     public function disableTwoFactor()
 *     {
 *         $this->two_factor_enabled = 0;
 *         $this->two_factor_secret = null;
 *         return $this->save(false, ['two_factor_enabled', 'two_factor_secret']);
 *     }
 * }
 * ```
 *
 * @author AlexeiKaDev
 * @since 1.1.0
 */
interface TwoFactorInterface
{
    /**
     * Check if two-factor authentication is enabled for this user.
     * @return bool
     */
    public function getIsTwoFactorEnabled();

    /**
     * Get the user's two-factor authentication secret.
     * @return string|null
     */
    public function getTwoFactorSecret();

    /**
     * Set the user's two-factor authentication secret.
     * @param string $secret
     * @return bool
     */
    public function setTwoFactorSecret($secret);

    /**
     * Enable two-factor authentication for this user.
     * @return bool
     */
    public function enableTwoFactor();

    /**
     * Disable two-factor authentication for this user.
     * @return bool
     */
    public function disableTwoFactor();
}
