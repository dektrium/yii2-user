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

/**
 * @author Faenir <defaenir@gmail.com>
 */
class m170109_114936_add_pk_token_table extends Migration
{
    /** @inheritdoc */
    public function up()
    {
        $this->alterColumn('{{%token}}', 'user_id', $this->primaryKey());
    }

    /** @inheritdoc */
    public function down()
    {
        $this->alterColumn('{{%token}}', 'user_id', $this->integer()->notNull());
    }
}
