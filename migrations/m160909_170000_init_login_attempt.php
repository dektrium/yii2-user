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
 * @author jkmssoft
 */
class m160909_170000_init_login_attempt extends Migration
{
    public function up()
    {
        $this->createTable('{{%login_attempt}}', [
            'id'                => Schema::TYPE_PK,
            'ip'                => Schema::TYPE_STRING . '(32)', // store only md5-sum!
            'attempts'          => Schema::TYPE_INTEGER . ' NULL',
            'last_attempt_at'   => Schema::TYPE_INTEGER . ' NULL',
        ], $this->tableOptions);

        $this->createIndex('login_attempt_unique_ip', '{{%login_attempt}}', 'ip', true);
    }

    public function down()
    {
        $this->dropIndex('login_attempt_unique_ip', '{{%login_attempt}}');
        $this->dropTable('{{%login_attempt}}');
    }
}
