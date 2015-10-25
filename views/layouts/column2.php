<?php

/* @var $this \yii\web\View */
use mariusz_soltys\yii2user\Module;
use mariusz_soltys\yii2user\UserAssets;
use yii\bootstrap\Nav;
use yii\widgets\Menu;

//UserAssets::register($this);

/* @var $content string */

$this->beginContent(Module::getInstance()->mainLayout); ?>

<?=
Nav::widget([
    'items' => Module::getInstance()->getMenu(),
    'options' => ['class' =>'nav-pills'], // set this to nav-tab to get tab-styled navigation
]);
?>

<?= $content ?>

<?php $this->endContent();
