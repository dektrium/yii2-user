<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use dektrium\user\migrations\Migration;
use yii\db\Schema;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class m140209_132017_init extends Migration
{
    public function up()
    {
        $this->createTable('{{%user}}', [
            'id' => Schema::TYPE_PK,
            'username'             => Schema::TYPE_STRING . '(25) NOT NULL',
            'email'                => Schema::TYPE_STRING . '(255) NOT NULL',
            'password_hash'        => Schema::TYPE_STRING . '(60) NOT NULL',
            'auth_key'             => Schema::TYPE_STRING . '(32) NOT NULL',
            'confirmation_token'   => Schema::TYPE_STRING . '(32) NULL',
            'confirmation_sent_at' => Schema::TYPE_INTEGER. ' NULL',
            'confirmed_at'         => Schema::TYPE_INTEGER. ' NULL',
            'unconfirmed_email'    => Schema::TYPE_STRING . '(255) NULL',
            'recovery_token'       => Schema::TYPE_STRING . '(32) NULL',
            'recovery_sent_at'     => Schema::TYPE_INTEGER. ' NULL',
            'blocked_at'           => Schema::TYPE_INTEGER. ' NULL',
            'registered_from'      => Schema::TYPE_INTEGER. ' NULL',
            'logged_in_from'       => Schema::TYPE_INTEGER. ' NULL',
            'logged_in_at'         => Schema::TYPE_INTEGER. ' NULL',
            'created_at'           => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at'           => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $this->tableOptions);

        $this->createIndex('{{%user_unique_username}}', '{{%user}}', 'username', true);
        $this->createIndex('{{%user_unique_email}}', '{{%user}}', 'email', true);
        $this->createIndex('{{%user_confirmation}}', '{{%user}}', 'id, confirmation_token', true);
        $this->createIndex('{{%user_recovery}}', '{{%user}}', 'id, recovery_token', true);

        $this->createTable('{{%profile}}', [
            'user_id'        => Schema::TYPE_INTEGER . ' PRIMARY KEY',
            'name'           => Schema::TYPE_STRING . '(255) NULL',
            'public_email'   => Schema::TYPE_STRING . '(255) NULL',
            'gravatar_email' => Schema::TYPE_STRING . '(255) NULL',
            'gravatar_id'    => Schema::TYPE_STRING . '(32) NULL',
            'location'       => Schema::TYPE_STRING . '(255) NULL',
            'website'        => Schema::TYPE_STRING . '(255) NULL',
            'bio'            => Schema::TYPE_TEXT. ' NULL',
        ], $this->tableOptions);

        $this->addForeignKey('{{%fk_user_profile}}', '{{%profile}}', 'user_id', '{{%user}}', 'id', $this->cascade, $this->restrict);
    }

    public function down()
    {
        $this->dropTable('{{%profile}}');
        $this->dropTable('{{%user}}');
    }
}
