<?php

use marsoltys\yii2user\Module;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var $form \marsoltys\yii2user\models\UserRecoveryForm
 */

$this->title=Yii::$app->name . ' - '.Module::t("Change password");
$this->params['breadcrumbs']= [
    ['label' => Module::t("Login"), 'url' => ['/user/login']],
    Module::t("Change password"),
];
?>

<div class="recover-password">

    <h1><?php echo Module::t("Change password"); ?></h1>

    <div class="col-lg-4">

        <?php $f = ActiveForm::begin([
            'id' => 'recover-password',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
        ]); ?>

        <p class="note"><?php echo Module::t('Fields with <span class="required">*</span> are required.'); ?></p>
        <?php //echo Html::errorSummary($form); ?>

        <?= $f->field($form, 'password')->passwordInput()->hint(Module::t("Minimal password length 4 symbols.")); ?>

        <?= $f->field($form, 'verifyPassword'); ?>


        <div class="form-group">
            <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-primary']); ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div><!-- form -->