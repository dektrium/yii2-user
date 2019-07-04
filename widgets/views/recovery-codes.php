<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\User $user
 * @var dektrium\user\models\Token[] $tokens
 * @var dektrium\user\widgets\RecoveryCodes $widget
 */

$tokens = $model;

?>
<?php
$lastIndex = count($tokens) - 1;
for ($i = 0, $length = count($tokens); $i < $length; $i++): ?>
    <td>
        <?= isset($tokens[$i]) ? $tokens[$i]->code : $tokens[$i] ?>
    </td>
<?php endfor; ?>