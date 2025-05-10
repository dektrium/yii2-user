<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @var yii\web\View $this
 * @var \AlexeiKaDev\Yii2User\Module $module
 */

use yii\bootstrap5\Alert;

?>

<?php if ($module->enableFlashMessages): ?>
    <div class="row">
        <div class="col-12">
            <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
                <?php if (in_array($type, ['success', 'danger', 'warning', 'info'], true)): ?>
                    <?= Alert::widget([
                        'body' => $message,
                        'options' => [
                            'class' => 'alert-' . $type . ' alert-dismissible fade show',
                        ],
                        'closeButton' => [],
                    ]); ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
