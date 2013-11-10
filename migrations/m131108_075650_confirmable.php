<?php

use yii\db\Schema;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class m131108_075650_confirmable extends \yii\db\Migration
{
    public function up()
    {
        $this->addColumn('{{user}}', 'confirmation_token', Schema::TYPE_STRING.'(32)');
        $this->addColumn('{{user}}', 'confirmation_time', Schema::TYPE_INTEGER);
        $this->addColumn('{{user}}', 'confirmation_sent_time', Schema::TYPE_INTEGER);
    }

    public function down()
    {
        $this->dropColumn('{{user}}', 'confirmation_sent_time');
        $this->dropColumn('{{user}}', 'confirmation_time');
        $this->dropColumn('{{user}}', 'confirmation_token');
    }
}
