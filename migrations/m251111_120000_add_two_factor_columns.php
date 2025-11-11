<?php

/*
 * This file is part of the AlexeiKaDev yii2-user project.
 *
 * (c) AlexeiKaDev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\migrations;

use yii\db\Schema;

/**
 * Adds two-factor authentication columns to user table.
 *
 * This migration adds optional support for Two-Factor Authentication (2FA).
 * To use 2FA, implement the TwoFactorInterface in your User model and install
 * a 2FA module like hiqdev/yii2-mfa or vxm/yii2-mfa.
 *
 * @author AlexeiKaDev
 * @since 1.1.0
 */
class m251111_120000_add_two_factor_columns extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'two_factor_enabled', Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0');
        $this->addColumn('{{%user}}', 'two_factor_secret', Schema::TYPE_STRING . '(255) DEFAULT NULL');

        $this->createIndex('idx_user_two_factor_enabled', '{{%user}}', 'two_factor_enabled');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('idx_user_two_factor_enabled', '{{%user}}');
        $this->dropColumn('{{%user}}', 'two_factor_secret');
        $this->dropColumn('{{%user}}', 'two_factor_enabled');
    }
}
