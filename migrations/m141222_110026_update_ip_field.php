<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use yii\db\Query;
use yii\db\Schema;
use yii\db\Migration;

class m141222_110026_update_ip_field extends Migration
{
    public function up()
    {
        $users = (new Query())->from('{{%user}}')->select('id, registration_ip ip')->all();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->alterColumn('{{%user}}', 'registration_ip', Schema::TYPE_STRING . '(45)');
            foreach ($users as $user) {
                if ($user['ip'] == null) continue;
                Yii::$app->db->createCommand()->update('{{%user}}', [
                    'registration_ip' => long2ip($user['ip'])
                ], 'id = ' . $user['id'])->execute();
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function down()
    {
        echo "m141222_110026_update_ip_field cannot be reverted.\n";

        return false;
    }
}
