<?php

declare(strict_types=1);

use AlexeiKaDev\Yii2User\migrations\Migration;

class m160929_103127_add_last_login_at_to_user_table extends Migration
{
    public function up(): void
    {
        $this->addColumn('{{%user}}', 'last_login_at', $this->integer());

    }

    public function down(): void
    {
        $this->dropColumn('{{%user}}', 'last_login_at');
    }
}
