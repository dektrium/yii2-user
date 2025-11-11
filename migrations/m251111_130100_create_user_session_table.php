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
 * Creates user_session table for tracking active sessions across multiple devices.
 *
 * @author AlexeiKaDev
 */
class m251111_130100_create_user_session_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%user_session}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'session_id' => $this->string(255)->notNull(),
            'ip_address' => $this->string(45)->null(),
            'user_agent' => $this->string(500)->null(),
            'device_name' => $this->string(100)->null(),
            'location' => $this->string(255)->null(),
            'is_current' => $this->boolean()->notNull()->defaultValue(0),
            'last_activity' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        // Add unique index on session_id
        $this->createIndex('idx_user_session_session_id', '{{%user_session}}', 'session_id', true);

        // Add index on user_id for faster queries
        $this->createIndex('idx_user_session_user_id', '{{%user_session}}', 'user_id');

        // Add index on last_activity for cleanup queries
        $this->createIndex('idx_user_session_last_activity', '{{%user_session}}', 'last_activity');

        // Add composite index for user session queries
        $this->createIndex('idx_user_session_user_activity', '{{%user_session}}', ['user_id', 'last_activity']);

        // Add foreign key to user table
        $this->addForeignKey(
            'fk_user_session_user',
            '{{%user_session}}',
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
        $this->dropForeignKey('fk_user_session_user', '{{%user_session}}');
        $this->dropTable('{{%user_session}}');
    }
}
