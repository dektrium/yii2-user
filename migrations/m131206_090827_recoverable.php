<?php

use yii\db\Schema;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class m131206_090827_recoverable extends \yii\db\Migration
{
	public function up()
	{
		$this->addColumn('{{user}}', 'recovery_token', Schema::TYPE_STRING . '(32)');
		$this->addColumn('{{user}}', 'recovery_sent_time', Schema::TYPE_INTEGER);
	}

	public function down()
	{
		$this->dropColumn('{{user}}', 'recovery_sent_time');
		$this->dropColumn('{{user}}', 'recovery_token');
	}
}
