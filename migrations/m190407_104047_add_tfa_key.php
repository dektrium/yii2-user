<?php

use yii\db\Migration;

class m190407_104047_add_tfa_key extends Migration
{
  public function safeUp()
  {
    $this->addColumn(
        '{{%user}}',
        'tfa_key',
        $this->string(32)->null()->after('auth_key')
    );

  }

  public function safeDown()
  {
    $this->dropColumn('{{%user}}', 'tfa_key');
  }
}
