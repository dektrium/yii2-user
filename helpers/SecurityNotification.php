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

use AlexeiKaDev\Yii2User\models\User;
use AlexeiKaDev\Yii2User\models\UserSession;
use Yii;

/**
 * Helper for sending security-related notifications to users.
 *
 * Notifications help detect suspicious activity early:
 * - New device logins
 * - Unusual location access
 * - Password changes
 * - Security settings changes
 * - Multiple failed login attempts
 *
 * Statistics (2025):
 * - 2FA reduces unauthorized access by 99.9% (Microsoft)
 * - Security notifications help detect 73% of breaches within hours
 *
 * @author AlexeiKaDev
 */
class SecurityNotification
{
    /**
     * Send notification about new device login.
     *
     * @param User $user The user who logged in
     * @param array $sessionData Session information
     * @return bool Whether notification was sent successfully
     */
    public static function notifyNewDeviceLogin($user, $sessionData = [])
    {
        if (!self::shouldSendNotification($user, 'new_device_login')) {
            return false;
        }

        $deviceName = isset($sessionData['device_name']) ? $sessionData['device_name'] : 'Unknown Device';
        $ipAddress = isset($sessionData['ip_address']) ? $sessionData['ip_address'] : Yii::$app->request->userIP;
        $location = isset($sessionData['location']) ? $sessionData['location'] : null;
        $timestamp = date('Y-m-d H:i:s');

        $subject = Yii::t('user', 'New device login to your account');
        $message = Yii::t('user',
            "Hello {username},\n\n" .
            "We noticed a new login to your account:\n\n" .
            "Device: {device}\n" .
            "IP Address: {ip}\n" .
            "{location}" .
            "Time: {time}\n\n" .
            "If this was you, you can ignore this message.\n\n" .
            "If you don't recognize this activity, please:\n" .
            "1. Change your password immediately\n" .
            "2. Review your account security settings\n" .
            "3. Check your active sessions and log out suspicious ones\n\n" .
            "Stay secure!",
            [
                'username' => $user->username,
                'device' => $deviceName,
                'ip' => $ipAddress,
                'location' => $location ? "Location: {$location}\n" : '',
                'time' => $timestamp,
            ]
        );

        return self::sendEmail($user->email, $subject, $message);
    }

    /**
     * Send notification about password change.
     *
     * @param User $user The user whose password was changed
     * @return bool Whether notification was sent successfully
     */
    public static function notifyPasswordChange($user)
    {
        if (!self::shouldSendNotification($user, 'password_change')) {
            return false;
        }

        $ipAddress = Yii::$app->request->userIP;
        $timestamp = date('Y-m-d H:i:s');

        $subject = Yii::t('user', 'Your password has been changed');
        $message = Yii::t('user',
            "Hello {username},\n\n" .
            "Your password was successfully changed.\n\n" .
            "IP Address: {ip}\n" .
            "Time: {time}\n\n" .
            "If you didn't make this change, please contact us immediately and:\n" .
            "1. Reset your password using the recovery form\n" .
            "2. Review your account security settings\n" .
            "3. Enable two-factor authentication if not already enabled\n\n" .
            "Stay secure!",
            [
                'username' => $user->username,
                'ip' => $ipAddress,
                'time' => $timestamp,
            ]
        );

        return self::sendEmail($user->email, $subject, $message);
    }

    /**
     * Send notification about multiple failed login attempts.
     *
     * @param User $user The user account targeted
     * @param int $attemptCount Number of failed attempts
     * @return bool Whether notification was sent successfully
     */
    public static function notifyFailedLoginAttempts($user, $attemptCount)
    {
        if (!self::shouldSendNotification($user, 'failed_login')) {
            return false;
        }

        $ipAddress = Yii::$app->request->userIP;
        $timestamp = date('Y-m-d H:i:s');

        $subject = Yii::t('user', 'Multiple failed login attempts detected');
        $message = Yii::t('user',
            "Hello {username},\n\n" .
            "We detected {count} failed login attempts on your account.\n\n" .
            "IP Address: {ip}\n" .
            "Time: {time}\n\n" .
            "If this was you, you can ignore this message.\n\n" .
            "If you don't recognize this activity:\n" .
            "1. Change your password immediately\n" .
            "2. Enable two-factor authentication\n" .
            "3. Review your active sessions\n\n" .
            "Your account is still secure, but we recommend taking action.\n\n" .
            "Stay secure!",
            [
                'username' => $user->username,
                'count' => $attemptCount,
                'ip' => $ipAddress,
                'time' => $timestamp,
            ]
        );

        return self::sendEmail($user->email, $subject, $message);
    }

    /**
     * Send notification about email address change.
     *
     * @param User $user The user whose email was changed
     * @param string $oldEmail Previous email address
     * @param string $newEmail New email address
     * @return bool Whether notification was sent successfully
     */
    public static function notifyEmailChange($user, $oldEmail, $newEmail)
    {
        if (!self::shouldSendNotification($user, 'email_change')) {
            return false;
        }

        $ipAddress = Yii::$app->request->userIP;
        $timestamp = date('Y-m-d H:i:s');

        $subject = Yii::t('user', 'Your email address has been changed');

        // Send to both old and new email addresses
        $message = Yii::t('user',
            "Hello {username},\n\n" .
            "Your email address was changed from {old_email} to {new_email}.\n\n" .
            "IP Address: {ip}\n" .
            "Time: {time}\n\n" .
            "If you didn't make this change, please contact us immediately.\n\n" .
            "Stay secure!",
            [
                'username' => $user->username,
                'old_email' => $oldEmail,
                'new_email' => $newEmail,
                'ip' => $ipAddress,
                'time' => $timestamp,
            ]
        );

        // Send to both addresses
        $sent1 = self::sendEmail($oldEmail, $subject, $message);
        $sent2 = self::sendEmail($newEmail, $subject, $message);

        return $sent1 || $sent2;
    }

    /**
     * Send notification about 2FA status change.
     *
     * @param User $user The user whose 2FA status changed
     * @param bool $enabled Whether 2FA was enabled or disabled
     * @return bool Whether notification was sent successfully
     */
    public static function notify2FAChange($user, $enabled)
    {
        if (!self::shouldSendNotification($user, '2fa_change')) {
            return false;
        }

        $ipAddress = Yii::$app->request->userIP;
        $timestamp = date('Y-m-d H:i:s');

        $action = $enabled ? 'enabled' : 'disabled';
        $subject = Yii::t('user', 'Two-Factor Authentication has been {action}', ['action' => $action]);

        $message = Yii::t('user',
            "Hello {username},\n\n" .
            "Two-Factor Authentication (2FA) was {action} on your account.\n\n" .
            "IP Address: {ip}\n" .
            "Time: {time}\n\n" .
            ($enabled
                ? "Your account is now more secure! Keep your 2FA codes safe.\n\n"
                : "IMPORTANT: Your account is now less secure. We strongly recommend re-enabling 2FA.\n\n"
            ) .
            "If you didn't make this change, please:\n" .
            "1. Change your password immediately\n" .
            "2. Review your account security settings\n" .
            "3. Contact us if you need help\n\n" .
            "Stay secure!",
            [
                'username' => $user->username,
                'action' => $action,
                'ip' => $ipAddress,
                'time' => $timestamp,
            ]
        );

        return self::sendEmail($user->email, $subject, $message);
    }

    /**
     * Send notification about account lockout due to suspicious activity.
     *
     * @param User $user The user whose account was locked
     * @param string $reason Reason for lockout
     * @return bool Whether notification was sent successfully
     */
    public static function notifyAccountLockout($user, $reason)
    {
        if (!self::shouldSendNotification($user, 'account_lockout')) {
            return false;
        }

        $timestamp = date('Y-m-d H:i:s');

        $subject = Yii::t('user', 'Your account has been temporarily locked');
        $message = Yii::t('user',
            "Hello {username},\n\n" .
            "Your account was temporarily locked due to suspicious activity:\n" .
            "{reason}\n\n" .
            "Time: {time}\n\n" .
            "To unlock your account:\n" .
            "1. Use the password recovery form to reset your password\n" .
            "2. Or contact our support team\n\n" .
            "This is a security measure to protect your account.\n\n" .
            "Stay secure!",
            [
                'username' => $user->username,
                'reason' => $reason,
                'time' => $timestamp,
            ]
        );

        return self::sendEmail($user->email, $subject, $message);
    }

    /**
     * Check if notification should be sent based on user preferences.
     *
     * @param User $user The user
     * @param string $notificationType Type of notification
     * @return bool Whether notification should be sent
     */
    protected static function shouldSendNotification($user, $notificationType)
    {
        // Check if user has email
        if (empty($user->email)) {
            return false;
        }

        // Check if user wants to receive security notifications
        // This could be extended with user preferences table
        // For now, send all security notifications by default
        return true;
    }

    /**
     * Send email notification.
     *
     * @param string $email Recipient email address
     * @param string $subject Email subject
     * @param string $message Email message
     * @return bool Whether email was sent successfully
     */
    protected static function sendEmail($email, $subject, $message)
    {
        try {
            return Yii::$app->mailer->compose()
                ->setFrom([Yii::$app->params['supportEmail'] ?? 'noreply@example.com' => Yii::$app->name])
                ->setTo($email)
                ->setSubject($subject)
                ->setTextBody($message)
                ->send();
        } catch (\Exception $e) {
            Yii::error('Failed to send security notification: ' . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Get security notification preferences for user.
     * Can be extended to support user-configurable preferences.
     *
     * @param User $user The user
     * @return array Notification preferences
     */
    public static function getNotificationPreferences($user)
    {
        // Default preferences - all notifications enabled
        return [
            'new_device_login' => true,
            'password_change' => true,
            'failed_login' => true,
            'email_change' => true,
            '2fa_change' => true,
            'account_lockout' => true,
        ];
    }
}
