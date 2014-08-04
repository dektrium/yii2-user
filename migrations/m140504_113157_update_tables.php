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
class m140504_113157_update_tables extends Migration
{
    public function up()
    {
        // user table
        $this->dropIndex('user_confirmation', '{{%user}}');
        $this->dropIndex('user_recovery', '{{%user}}');
        $this->dropColumn('{{%user}}', 'confirmation_token');
        $this->dropColumn('{{%user}}', 'confirmation_sent_at');
        $this->dropColumn('{{%user}}', 'recovery_token');
        $this->dropColumn('{{%user}}', 'recovery_sent_at');
        $this->dropColumn('{{%user}}', 'logged_in_from');
        $this->dropColumn('{{%user}}', 'logged_in_at');
        $this->renameColumn('{{%user}}', 'registered_from', 'registration_ip');
        $this->addColumn('{{%user}}', 'flags', Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0');

        // account table
        $this->renameColumn('{{%account}}', 'properties', 'data');
    }

    public function down()
    {
        return false;
    }
}
