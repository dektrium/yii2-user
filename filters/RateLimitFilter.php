<?php

/*
 * This file is part of the AlexeiKaDev yii2-user project.
 *
 * (c) AlexeiKaDev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\filters;

use yii\filters\RateLimiter;

/**
 * Rate Limit Filter for protecting against brute-force attacks.
 *
 * Usage example in your SecurityController:
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'rateLimiter' => [
 *             'class' => RateLimitFilter::class,
 *             'only' => ['login'],
 *             'user' => function () {
 *                 // Get user by username from POST data
 *                 $username = Yii::$app->request->post('login-form')['login'] ?? null;
 *                 if ($username) {
 *                     return User::findByUsername($username);
 *                 }
 *                 return null;
 *             },
 *         ],
 *     ];
 * }
 * ```
 *
 * For this to work, your User model must implement RateLimitableInterface:
 * ```php
 * class User extends ActiveRecord implements RateLimitableInterface
 * {
 *     public function getRateLimit($request, $action)
 *     {
 *         return [5, 60]; // 5 attempts per 60 seconds
 *     }
 *
 *     public function loadAllowance($request, $action)
 *     {
 *         return [$this->allowance, $this->allowance_updated_at];
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
class RateLimitFilter extends RateLimiter
{
    /**
     * @var int Maximum number of requests allowed per time window
     */
    public $maxRequests = 5;

    /**
     * @var int Time window in seconds
     */
    public $timeWindow = 60;

    /**
     * @var bool Whether to enable rate limiting for guests
     */
    public $enableForGuests = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->user === null && $this->enableForGuests) {
            // For guests, use IP-based rate limiting
            $this->user = function () {
                // Create a simple guest user object for rate limiting
                return new class {
                    private $_allowance = null;
                    private $_allowance_updated_at = null;

                    public function getRateLimit($request, $action)
                    {
                        return [5, 60]; // 5 attempts per 60 seconds for guests
                    }

                    public function loadAllowance($request, $action)
                    {
                        $key = 'rate_limit_' . $request->getUserIP();
                        $data = \Yii::$app->cache->get($key);
                        if ($data === false) {
                            return [5, time()];
                        }
                        return [$data['allowance'], $data['timestamp']];
                    }

                    public function saveAllowance($request, $action, $allowance, $timestamp)
                    {
                        $key = 'rate_limit_' . $request->getUserIP();
                        \Yii::$app->cache->set($key, [
                            'allowance' => $allowance,
                            'timestamp' => $timestamp,
                        ], 60);
                    }
                };
            };
        }
    }
}
