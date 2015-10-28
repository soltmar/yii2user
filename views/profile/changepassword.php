<?php

use mariusz_soltys\yii2user\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title=Yii::$app->name . ' - '.Module::t("Change password");
$this->params['breadcrumbs']=array(
   ['label' => Module::t("Profile"), 'url' => array('/user/profile')],
    Module::t("Change password"),
);
$menu= [
    ['label'=>Module::t('List User'), 'url'=> ['/user']],
    ['label'=>Module::t('Profile'), 'url'=> ['/user/profile']],
    ['label'=>Module::t('Edit'), 'url'=> ['/user/profile/edit']],
    ['label'=>Module::t('Logout'), 'url'=> ['/user/logout']],
];

if (Module::isAdmin()) {
    array_unshift($menu, ['label'=>Module::t('Manage Users'), 'url'=> ['/user/admin']]);
}

Module::getInstance()->setMenu($menu);
?>

<h1><?php echo Module::t("Change password"); ?></h1>

<div class="form">

    <?php $form = ActiveForm::begin([
        'id'=>'changepassword-form',
        'enableAjaxValidation'=>true,
        'validateOnSubmit'=>true,
    ]); ?>

    <p class="note"><?php echo Module::t('Fields with <span class="required">*</span> are required.'); ?></p>
    <?php echo $form->errorSummary($model); ?>

    <?= $form->field($model, 'oldPassword')->passwordInput(); ?>

    <?= $form->field($model, 'password')->passwordInput()
        ->hint(Module::t("Minimal password length 4 symbols.")); ?>

    <?= $form->field($model, 'verifyPassword')->passwordInput(); ?>

    <div class="form-group">
        <?=
        Html::submitButton(
            Module::t("Save"),
            ['class' => 'btn btn-primary']
        ); ?>
    </div>

    <?php ActiveForm::end(); ?>
</div><!-- form -->