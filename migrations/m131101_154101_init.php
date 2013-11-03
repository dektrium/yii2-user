<?php

use yii\db\Schema;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class m131101_154101_init extends \yii\db\Migration
{
    public function up()
    {
        // MySQL-specific table options. Adjust if you plan working with another DBMS
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('{{user}}', [
            // general
            'id'            => Schema::TYPE_PK,
            'username'      => Schema::TYPE_STRING.'(25) NOT NULL',
            'email'         => Schema::TYPE_STRING.' NOT NULL',
            'password_hash' => Schema::TYPE_STRING.'(60) NOT NULL',
            'auth_key'      => Schema::TYPE_STRING.'(32) NOT NULL',
            // timestamps
            'create_time'   => Schema::TYPE_INTEGER.' NOT NULL',
            'update_time'   => Schema::TYPE_INTEGER.' NOT NULL'
        ], $tableOptions);

        $this->createIndex('username_unique', '{{user}}', 'username', true);
        $this->createIndex('email_unique', '{{user}}', 'email', true);
    }

    public function down()
    {
        $this->dropIndex('username_unique', '{{user}}');
        $this->dropIndex('email_unique', '{{user}}');
        $this->dropTable('{{user}}');
    }
}