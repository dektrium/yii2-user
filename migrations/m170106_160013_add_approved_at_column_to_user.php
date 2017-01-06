<?php

use yii\db\Migration;

class m170106_160013_add_approved_at_column_to_user extends Migration
{
    public function safeUp()
    {
        $this->addColumn('user', 'approved_at', $this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn('user', 'approved_at');
    }
}
