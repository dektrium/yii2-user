<div class="row">
    <div class="col-xs-12">
        <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
            <?php if (in_array($type, ['success', 'danger', 'warning', 'info'])): ?>
                <div class="alert alert-<?= $type ?>">
                    <?= $message ?>
                </div>
            <?php endif ?>
        <?php endforeach ?>
    </div>
</div>