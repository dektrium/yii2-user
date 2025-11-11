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
 * Adds rate limiting columns to user table.
 *
 * This migration adds support for rate limiting to protect against brute-force attacks.
 * To use rate limiting, implement the RateLimitableInterface in your User model
 * and configure the rate limiter behavior in your controllers.
 *
 * @author AlexeiKaDev
 * @since 1.1.0
 */
class m251111_120100_add_rate_limiting_columns extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'allowance', Schema::TYPE_INTEGER . ' DEFAULT 0');
        $this->addColumn('{{%user}}', 'allowance_updated_at', Schema::TYPE_INTEGER . ' DEFAULT 0');

        $this->createIndex('idx_user_allowance', '{{%user}}', 'allowance_updated_at');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('idx_user_allowance', '{{%user}}');
        $this->dropColumn('{{%user}}', 'allowance_updated_at');
        $this->dropColumn('{{%user}}', 'allowance');
    }
}
