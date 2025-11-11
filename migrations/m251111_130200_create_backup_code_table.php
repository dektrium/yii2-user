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
 * Creates backup_code table for Two-Factor Authentication recovery codes.
 *
 * @author AlexeiKaDev
 */
class m251111_130200_create_backup_code_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%backup_code}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'code_hash' => $this->string(255)->notNull(),
            'used' => $this->boolean()->notNull()->defaultValue(0),
            'used_at' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        // Add index on user_id for faster queries
        $this->createIndex('idx_backup_code_user_id', '{{%backup_code}}', 'user_id');

        // Add index on used status for finding available codes
        $this->createIndex('idx_backup_code_used', '{{%backup_code}}', 'used');

        // Add composite index for user + used queries
        $this->createIndex('idx_backup_code_user_used', '{{%backup_code}}', ['user_id', 'used']);

        // Add foreign key to user table
        $this->addForeignKey(
            'fk_backup_code_user',
            '{{%backup_code}}',
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
        $this->dropForeignKey('fk_backup_code_user', '{{%backup_code}}');
        $this->dropTable('{{%backup_code}}');
    }
}
