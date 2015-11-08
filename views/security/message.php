<?php use mariusz_soltys\yii2user\Module;

$this->title=Yii::$app->name . ' - '.Module::t("Login"); ?>

<h1><?= $title; ?></h1>

<div class="form">
<?= $content; ?>
</div><!-- yiiForm -->