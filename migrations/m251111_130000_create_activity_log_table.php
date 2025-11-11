<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use yii\db\Migration;

/**
 * Creates activity_log table for tracking user actions (GDPR Article 30 compliance).
 *
 * @author AlexeiKaDev
 */
class m251111_130000_create_activity_log_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%activity_log}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'action' => $this->string(50)->notNull(),
            'ip_address' => $this->string(45)->null(),
            'user_agent' => $this->string(255)->null(),
            'location' => $this->string(255)->null(),
            'metadata' => $this->text()->null(),
            'created_at' => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        // Add index on user_id for faster queries
        $this->createIndex('idx_activity_log_user_id', '{{%activity_log}}', 'user_id');

        // Add index on action for filtering by action type
        $this->createIndex('idx_activity_log_action', '{{%activity_log}}', 'action');

        // Add index on created_at for time-based queries and cleanup
        $this->createIndex('idx_activity_log_created_at', '{{%activity_log}}', 'created_at');

        // Add composite index for user activity queries
        $this->createIndex('idx_activity_log_user_created', '{{%activity_log}}', ['user_id', 'created_at']);

        // Add foreign key to user table
        $this->addForeignKey(
            'fk_activity_log_user',
            '{{%activity_log}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_activity_log_user', '{{%activity_log}}');
        $this->dropTable('{{%activity_log}}');
    }
}
