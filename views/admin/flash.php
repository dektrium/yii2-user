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
 */

?>

<?php if (Yii::$app->getSession()->hasFlash('user.success')): ?>
    <div class="alert alert-success">
        <p><?= Yii::$app->getSession()->getFlash('user.success') ?></p>
    </div>
<?php endif; ?>

<?php if (Yii::$app->getSession()->hasFlash('user.error')): ?>
    <div class="alert alert-danger">
        <p><?= Yii::$app->getSession()->getFlash('user.error') ?></p>
    </div>
<?php endif; ?>