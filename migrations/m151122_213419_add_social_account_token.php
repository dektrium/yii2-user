<?php

use yii\db\Schema;
use yii\db\Migration;

class m151122_213419_add_social_account_token extends Migration
{
    public function up()
    {
        $this->addColumn('{{%social_account}}', 'token', Schema::TYPE_STRING . '(255) NULL');
    }

    public function down()
    {
        $this->alterColumn('{{%social_account}}', 'token', Schema::TYPE_STRING . '(255)');
    }
}
