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

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class m140209_132017_init extends Migration
{
    public function up()
    {
        $this->createTable('{{%user}}', [
            'id'                   => $this->primaryKey(),
            'username'             => $this->string(25)->notNull(),
            'email'                => $this->string(255)->notNull(),
            'password_hash'        => $this->string(60)->notNull(),
            'auth_key'             => $this->string(32)->notNull(),
            'confirmation_token'   => $this->string(32),
            'confirmation_sent_at' => $this->integer(),
            'confirmed_at'         => $this->integer(),
            'unconfirmed_email'    => $this->string(255),
            'recovery_token'       => $this->string(32),
            'recovery_sent_at'     => $this->integer(),
            'blocked_at'           => $this->integer(),
            'registered_from'      => $this->integer(),
            'logged_in_from'       => $this->integer(),
            'logged_in_at'         => $this->integer(),
            'created_at'           => $this->integer()->notNull(),
            'updated_at'           => $this->integer()->notNull(),
        ], $this->tableOptions);

        $this->createIndex('user_unique_username', '{{%user}}', 'username', true);
        $this->createIndex('user_unique_email', '{{%user}}', 'email', true);
        $this->createIndex('user_confirmation', '{{%user}}', 'id, confirmation_token', true);
        $this->createIndex('user_recovery', '{{%user}}', 'id, recovery_token', true);

        $this->createTable('{{%profile}}', [
            'user_id'        => $this->primaryKey(),
            'name'           => $this->string(255),
            'public_email'   => $this->string(255),
            'gravatar_email' => $this->string(255),
            'gravatar_id'    => $this->string(32),
            'location'       => $this->string(255),
            'website'        => $this->string(255),
            'bio'            => $this->text(),
        ], $this->tableOptions);

        $this->addForeignKey('fk_user_profile', '{{%profile}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%profile}}');
        $this->dropTable('{{%user}}');
    }
}
