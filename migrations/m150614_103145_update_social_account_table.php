<?php

use yii\db\Query;
use dektrium\user\migrations\Migration;

class m150614_103145_update_social_account_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%social_account}}', 'code', $this->string(32)->null());
        $this->addColumn('{{%social_account}}', 'created_at', $this->integer()->null());
        $this->addColumn('{{%social_account}}', 'email', $this->string()->null());
        $this->addColumn('{{%social_account}}', 'username', $this->string()->null());
        $this->createIndex('{{%account_unique_code}}', '{{%social_account}}', 'code', true);

        $accounts = (new Query())->from('{{%social_account}}')->select('id')->all($this->db);

        $transaction = $this->db->beginTransaction();
        try {
            foreach ($accounts as $account) {
                $this->db->createCommand()->update('{{%social_account}}', [
                    'created_at' => time(),
                ], 'id = ' . $account['id'])->execute();
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function down()
    {
        $this->dropIndex('{{%account_unique_code}}', '{{%social_account}}');
        $this->dropColumn('{{%social_account}}', 'email');
        $this->dropColumn('{{%social_account}}', 'username');
        $this->dropColumn('{{%social_account}}', 'code');
        $this->dropColumn('{{%social_account}}', 'created_at');
    }
}
