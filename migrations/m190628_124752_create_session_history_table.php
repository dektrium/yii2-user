<?php

use dektrium\user\migrations\Migration;
use yii\web\DbSession;

/**
 * Class m190628_124752_session_history
 */
class m190628_124752_create_session_history_table extends Migration
{
    const SESSION_HISTORY_TABLE = '{{%session_history}}';
    const USER_TABLE = '{{%user}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::SESSION_HISTORY_TABLE, [
            'user_id' => $this->integer(),
            'session_id' => $this->string()->null(),
            'user_agent' => $this->string()->notNull(),
            'ip' => $this->string(45)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            '{{%session_history_user_id}}',
            self::SESSION_HISTORY_TABLE,
            ['user_id']
        );

        $this->createIndex(
            '{{%session_history_session_id}}',
            self::SESSION_HISTORY_TABLE,
            ['session_id']
        );

        $this->createIndex(
            '{{%session_history_updated_at}}',
            self::SESSION_HISTORY_TABLE,
            ['updated_at']
        );

        $this->addForeignKey(
            '{{%fk_user_session_history}}',
            self::SESSION_HISTORY_TABLE,
            'user_id',
            self::USER_TABLE,
            'id',
            $this->cascade,
            $this->restrict
        );

        $session = Yii::$app->session;
        if ($session instanceof DbSession && $this->db === $session->db) {
            $this->addForeignKey(
                '{{%fk_session_session_history}}',
                self::SESSION_HISTORY_TABLE,
                'session_id',
                $session->sessionTable,
                'id',
                'SET NULL',
                $this->restrict
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::SESSION_HISTORY_TABLE);
    }
}
