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
class m140504_130433_create_profile_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%profile}}', [
            'user_id'        => Schema::TYPE_INTEGER . ' PRIMARY KEY',
            'name'           => Schema::TYPE_STRING,
            'public_email'   => Schema::TYPE_STRING,
            'gravatar_email' => Schema::TYPE_STRING,
            'gravatar_id'    => Schema::TYPE_STRING . '(32)',
            'location'       => Schema::TYPE_STRING,
            'website'        => Schema::TYPE_STRING,
            'bio'            => Schema::TYPE_TEXT
        ], $this->tableOptions);

        $this->addForeignKey('fk_user_profile', '{{%profile}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%profile}}');
    }
}
