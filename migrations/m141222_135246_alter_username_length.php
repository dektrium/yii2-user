<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use yii\db\Migration;

class m141222_135246_alter_username_length extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%user}}', 'username', $this->string(255));
    }

    public function down()
    {
        $this->alterColumn('{{%user}}', 'username', $this->string(25));
    }
}
