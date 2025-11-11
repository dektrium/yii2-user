<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\helpers;

use Yii;

/**
 * Helper for checking passwords against HaveIBeenPwned database.
 *
 * Uses k-anonymity API for privacy protection (NIST-recommended).
 * Only sends first 5 characters of SHA-1 hash to the API.
 *
 * Statistics (2025):
 * - 81% of security breaches involve stolen credentials
 * - Over 15 billion breached passwords in database
 * - API processes 20+ million requests per month
 *
 * @author AlexeiKaDev
 */
class HaveIBeenPwned
{
    /**
     * HaveIBeenPwned Pwned Passwords API endpoint (k-anonymity).
     */
    const API_URL = 'https://api.pwnedpasswords.com/range/';

    /**
     * Check if a password has been compromised in known data breaches.
     *
     * Uses k-anonymity API - only sends first 5 characters of SHA-1 hash.
     * Your actual password never leaves your server.
     *
     * @param string $password The password to check
     * @return array ['breached' => bool, 'count' => int|null]
     *               breached: true if password found in breaches
     *               count: number of times password appeared in breaches (null if not breached)
     */
    public static function checkPassword($password)
    {
        try {
            // Generate SHA-1 hash of password (uppercase)
            $hash = strtoupper(sha1($password));

            // Split hash: first 5 chars for API, rest for local comparison
            $prefix = substr($hash, 0, 5);
            $suffix = substr($hash, 5);

            // Make API request with k-anonymity (only send first 5 chars)
            $response = self::makeRequest(self::API_URL . $prefix);

            if ($response === false) {
                // API error - log but don't block user (fail open for availability)
                Yii::error('HaveIBeenPwned API request failed', __METHOD__);
                return ['breached' => false, 'count' => null];
            }

            // Parse response: format is "SUFFIX:COUNT" per line
            $hashes = explode("\r\n", $response);

            foreach ($hashes as $line) {
                $parts = explode(':', $line);
                if (count($parts) === 2) {
                    $hashSuffix = trim($parts[0]);
                    $count = (int)trim($parts[1]);

                    // Check if our hash suffix matches
                    if (strcasecmp($hashSuffix, $suffix) === 0) {
                        return ['breached' => true, 'count' => $count];
                    }
                }
            }

            // Password not found in breaches
            return ['breached' => false, 'count' => 0];

        } catch (\Exception $e) {
            // Log error but don't block user
            Yii::error('HaveIBeenPwned check failed: ' . $e->getMessage(), __METHOD__);
            return ['breached' => false, 'count' => null];
        }
    }

    /**
     * Check if password is breached and return user-friendly message.
     *
     * @param string $password The password to check
     * @return array ['safe' => bool, 'message' => string|null]
     */
    public static function checkPasswordWithMessage($password)
    {
        $result = self::checkPassword($password);

        if ($result['breached']) {
            $message = Yii::t('user',
                'This password has been found in {count} data breaches. Please choose a different password.',
                ['count' => number_format($result['count'])]
            );

            return ['safe' => false, 'message' => $message];
        }

        return ['safe' => true, 'message' => null];
    }

    /**
     * Make HTTP request to HaveIBeenPwned API.
     *
     * @param string $url The API URL
     * @return string|false Response body or false on error
     */
    protected static function makeRequest($url)
    {
        // Use cURL if available (recommended)
        if (function_exists('curl_init')) {
            return self::makeCurlRequest($url);
        }

        // Fallback to file_get_contents
        return self::makeFileGetContentsRequest($url);
    }

    /**
     * Make HTTP request using cURL.
     *
     * @param string $url The API URL
     * @return string|false Response body or false on error
     */
    protected static function makeCurlRequest($url)
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'yii2-user-alexeikadev/1.0',
            CURLOPT_HTTPHEADER => [
                'Add-Padding: true', // OWASP recommendation: prevent timing attacks
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            Yii::error("cURL error: {$error}", __METHOD__);
            return false;
        }

        if ($httpCode !== 200) {
            Yii::error("HaveIBeenPwned API returned HTTP {$httpCode}", __METHOD__);
            return false;
        }

        return $response;
    }

    /**
     * Make HTTP request using file_get_contents (fallback).
     *
     * @param string $url The API URL
     * @return string|false Response body or false on error
     */
    protected static function makeFileGetContentsRequest($url)
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 5,
                'user_agent' => 'yii2-user-alexeikadev/1.0',
                'header' => "Add-Padding: true\r\n",
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            Yii::error('file_get_contents failed for HaveIBeenPwned API', __METHOD__);
            return false;
        }

        return $response;
    }

    /**
     * Validator for checking password against HaveIBeenPwned.
     *
     * Usage in model rules:
     * ```php
     * ['password', [HaveIBeenPwned::class, 'validatePassword']]
     * ```
     *
     * @param \yii\base\Model $model The model being validated
     * @param string $attribute The attribute being validated
     */
    public static function validatePassword($model, $attribute)
    {
        $password = $model->$attribute;

        if (empty($password)) {
            return; // Let required validator handle empty values
        }

        $result = self::checkPassword($password);

        if ($result['breached'] && $result['count'] !== null) {
            $model->addError(
                $attribute,
                Yii::t('user',
                    'This password has been compromised in data breaches ({count} times). Please choose a different password.',
                    ['count' => number_format($result['count'])]
                )
            );
        }
    }
}
