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
class m140504_130438_create_account_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%account}}', [
            'id'        => Schema::TYPE_PK,
            'user_id'   => Schema::TYPE_INTEGER . ' NOT NULL',
            'provider'  => Schema::TYPE_STRING . ' NOT NULL',
            'client_id' => Schema::TYPE_STRING . ' NOT NULL',
            'data'      => Schema::TYPE_TEXT
        ], $this->tableOptions);

        $this->createIndex('account_unique', '{{%account}}', ['provider', 'client_id'], true);
        $this->addForeignKey('fk_user_account', '{{%account}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%account}}');
    }
}
