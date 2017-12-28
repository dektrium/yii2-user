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
use yii\db\Query;

class m141222_110026_update_ip_field extends Migration
{
    public function up()
    {
        $users = (new Query())->from('{{%user}}')->select('id, registration_ip ip')->all($this->db);

        $transaction = $this->db->beginTransaction();
        try {
            $this->alterColumn('{{%user}}', 'registration_ip', $this->string(45));
            foreach ($users as $user) {
                if ($user['ip'] == null) {
                    continue;
                }
                $this->db->createCommand()->update('{{%user}}', [
                    'registration_ip' => long2ip($user['ip']),
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
        $users = (new Query())->from('{{%user}}')->select('id, registration_ip ip')->all($this->db);

        $transaction = $this->db->beginTransaction();
        try {
            foreach ($users as $user) {
                if ($user['ip'] == null) {
                    continue;
                }
                $this->db->createCommand()->update('{{%user}}', [
                    'registration_ip' => ip2long($user['ip'])
                ], 'id = ' . $user['id'])->execute();
            }
            if ($this->dbType == 'pgsql') {
                $this->alterColumn('{{%user}}', 'registration_ip', $this->bigInteger() . ' USING registration_ip::bigint');
            } else {
                $this->alterColumn('{{%user}}', 'registration_ip', $this->bigInteger());
            }

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
