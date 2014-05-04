<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use yii\db\Schema;
use dektrium\user\migrations\Migration;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class m140504_113157_create_user_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%user}}', [
            'id'              => Schema::TYPE_PK,
            'username'        => Schema::TYPE_STRING . ' NOT NULL',
            'email'           => Schema::TYPE_STRING . ' NOT NULL',
            'password_hash'   => Schema::TYPE_STRING . '(60) NOT NULL',
            'auth_key'        => Schema::TYPE_STRING . '(32) NOT NULL',
            'registration_ip' => Schema::TYPE_INTEGER,
            'created_at'      => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at'      => Schema::TYPE_INTEGER . ' NOT NULL',
            'confirmed_at'    => Schema::TYPE_INTEGER,
            'blocked_at'      => Schema::TYPE_INTEGER,
            'flags'           => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
        ], $this->tableOptions);

        $this->createIndex('user_unique_username', '{{%user}}', 'username', true);
        $this->createIndex('user_unique_email', '{{%user}}', 'email', true);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
