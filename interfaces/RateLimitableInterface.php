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
 * Rate Limitable Interface
 *
 * Implement this interface in your User model to enable rate limiting.
 * This helps protect against brute-force attacks.
 *
 * Example implementation:
 * ```php
 * class User extends ActiveRecord implements RateLimitableInterface
 * {
 *     public function getRateLimit($request, $action)
 *     {
 *         // Allow 5 login attempts
 *         return [5, 60]; // 5 attempts per 60 seconds
 *     }
 *
 *     public function loadAllowance($request, $action)
 *     {
 *         return [
 *             $this->allowance,
 *             $this->allowance_updated_at
 *         ];
 *     }
 *
 *     public function saveAllowance($request, $action, $allowance, $timestamp)
 *     {
 *         $this->allowance = $allowance;
 *         $this->allowance_updated_at = $timestamp;
 *         $this->save(false, ['allowance', 'allowance_updated_at']);
 *     }
 * }
 * ```
 *
 * @author AlexeiKaDev
 * @since 1.1.0
 */
interface RateLimitableInterface
{
    /**
     * Returns the maximum number of allowed requests and the window size.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @return array an array of two elements. The first element is the maximum number of allowed requests,
     * and the second element is the size of the window in seconds.
     */
    public function getRateLimit($request, $action);

    /**
     * Loads the number of allowed requests and the corresponding timestamp from a persistent storage.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @return array an array of two elements. The first element is the number of allowed requests,
     * and the second element is the corresponding UNIX timestamp.
     */
    public function loadAllowance($request, $action);

    /**
     * Saves the number of allowed requests and the corresponding timestamp to a persistent storage.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @param int $allowance the number of allowed requests remaining.
     * @param int $timestamp the current timestamp.
     */
    public function saveAllowance($request, $action, $allowance, $timestamp);
}
