<?php

use yii\db\Migration;

class m160929_103127_add_last_login_to_user_table extends Migration
{
  public function up()
  {
    $this->addColumn('user', 'last_login', $this->integer());

  }

  public function down()
  {
    $this->dropColumn('user', 'last_login');
  }
}
