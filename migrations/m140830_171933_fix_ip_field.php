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
use yii\db\Migration;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class m140830_171933_fix_ip_field extends Migration
{
    public function up()
    {
        switch (\Yii::$app->db->driverName) {
            case 'mysql':
                $this->alterColumn('{{%user}}', 'registration_ip', Schema::TYPE_INTEGER . ' UNSIGNED');
                break;
            case 'pgsql':
                $this->db->createCommand('ALTER TABLE {{%user}} ADD CONSTRAINT registrationIpCheck CHECK ([[registration_ip]] >= 0);')->execute();
                break;
            default:
                throw new \RuntimeException('Your database is not supported!');
        }
    }

    public function down()
    {
        switch (\Yii::$app->db->driverName) {
            case 'mysql':
                $this->alterColumn('{{%user}}', 'registration_ip', Schema::TYPE_INTEGER);
                break;
            case 'pgsql':
                $this->db->createCommand('ALTER TABLE {{%user}} DROP CONSTRAINT registrationIpCheck;')->execute();
                break;
            default:
                throw new \RuntimeException('Your database is not supported!');
        }
    }
}
