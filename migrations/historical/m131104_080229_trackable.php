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

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class m131104_080229_trackable extends \yii\db\Migration
{
  public function up()
  {
    $this->addColumn('{{%user}}', 'registration_ip', Schema::TYPE_INTEGER . ' UNSIGNED');
    $this->addColumn('{{%user}}', 'login_ip', Schema::TYPE_INTEGER . ' UNSIGNED');
    $this->addColumn('{{%user}}', 'login_time', Schema::TYPE_INTEGER);
  }

  public function down()
  {
    $this->dropColumn('{{%user}}', 'login_time');
    $this->dropColumn('{{%user}}', 'login_ip');
    $this->dropColumn('{{%user}}', 'registration_ip');
  }
}
