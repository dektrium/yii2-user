<?php

declare(strict_types=1);

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use AlexeiKaDev\Yii2User\migrations\Migration;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class m140830_172703_change_account_table_name extends Migration
{
    public function up(): void
    {
        $this->renameTable('{{%account}}', '{{%social_account}}');
    }

    public function down(): void
    {
        $this->renameTable('{{%social_account}}', '{{%account}}');
    }
}
