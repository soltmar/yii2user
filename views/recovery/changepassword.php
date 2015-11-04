<?php

use mariusz_soltys\yii2user\Module;
use yii\helpers\Html;

/**
 * @var $form \mariusz_soltys\yii2user\models\UserRecoveryForm
 */

$this->title=Yii::$app->name . ' - '.Module::t("Change password");
$this->breadcrumbs=array(
    Module::t("Login") => array('/user/login'),
    Module::t("Change password"),
);
?>

<h1><?php echo Module::t("Change password"); ?></h1>


<div class="form">
    <?php echo Html::beginForm(); ?>

    <p class="note"><?php echo Module::t('Fields with <span class="required">*</span> are required.'); ?></p>
    <?php echo Html::errorSummary($form); ?>

    <div class="row">
        <?php echo Html::activeLabel($form, 'password'); ?>
        <?php echo Html::activePasswordInput($form, 'password'); ?>
        <p class="hint">
            <?php echo Module::t("Minimal password length 4 symbols."); ?>
        </p>
    </div>

    <div class="row">
        <?php echo Html::activeLabel($form, 'verifyPassword'); ?>
        <?php echo Html::activePasswordInput($form, 'verifyPassword'); ?>
    </div>


    <div class="row submit">
        <?php echo Html::submitButton(Module::t("Save")); ?>
    </div>

    <?php echo Html::endForm(); ?>
</div><!-- form -->