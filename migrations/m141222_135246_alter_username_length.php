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

class m141222_135246_alter_username_length extends Migration
{
    public function up()
    {
        if ($this->dbType == 'sqlsrv') {
            $this->dropIndex('{{%user_unique_username}}', '{{%user}}');
        }
        if ($this->dbType == 'pgsql') {
            $this->alterColumn('{{%user}}', 'username', 'SET NOT NULL');
        } else {
            $this->alterColumn('{{%user}}', 'username', $this->string(255)->notNull());
        }
        if ($this->dbType == 'sqlsrv') {
            $this->createIndex('{{%user_unique_username}}', '{{%user}}', 'username', true);
        }
    }

    public function down()
    {
        if ($this->dbType == 'sqlsrv') {
            $this->dropIndex('{{%user_unique_username}}', '{{%user}}');
        }
        if ($this->dbType == 'pgsql') {
            $this->alterColumn('{{%user}}', 'username', 'DROP NOT NULL');
        } else {
            $this->alterColumn('{{%user}}', 'username', $this->string(255)->notNull());
        }
        if ($this->dbType == 'sqlsrv') {
            $this->createIndex('{{%user_unique_username}}', '{{%user}}', 'username', true);
        }
    }
}
