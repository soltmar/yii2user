<?php

use mariusz_soltys\yii2user\Module;
use yii\helpers\Html;

//$this->pageTitle=Yii::$app->name . ' - '.Module::t("Login");
//$this->breadcrumbs=array(
//	Module::t("Login"),
//);
?>

<h1><?= Module::t("Login"); ?></h1>

<?php if(Yii::$app->user->hasFlash('loginMessage')): ?>

<div class="success">
	<?= Yii::$app->user->getFlash('loginMessage'); ?>
</div>

<?php endif; ?>

<p><?= Module::t("Please fill out the following form with your login credentials:"); ?></p>

<div class="form">
<?= Html::beginForm(); ?>

	<p class="note"><?= Module::t('Fields with <span class="required">*</span> are required.'); ?></p>

	<?= Html::errorSummary($model); ?>

	<div class="row">
		<?= Html::activeLabel($model, 'username'); ?>
		<?= Html::activeTextInput($model, 'username') ?>
	</div>

	<div class="row">
		<?= Html::activeLabel($model, 'password'); ?>
		<?= Html::activePasswordInput($model, 'password') ?>
	</div>

	<div class="row">
		<p class="hint">
		<?= Html::a(Module::t("Register"), Yii::$app->getModule('user')->registrationUrl); ?>
			|
		<?= Html::link(Module::t("Lost Password?"), Yii::$app->getModule('user')->recoveryUrl); ?>
		</p>
	</div>

	<div class="row rememberMe">
		<?= Html::activeCheckBox($model, 'rememberMe'); ?>
		<?= Html::activeLabel($model, 'rememberMe'); ?>
	</div>

	<div class="row submit">
		<?= Html::submitButton(Module::t("Login")); ?>
	</div>

<?= Html::endForm(); ?>
</div><!-- form -->


<?php
//$form = new CForm(array(
//    'elements'=>array(
//        'username'=>array(
//            'type'=>'text',
//            'maxlength'=>32,
//        ),
//        'password'=>array(
//            'type'=>'password',
//            'maxlength'=>32,
//        ),
//        'rememberMe'=>array(
//            'type'=>'checkbox',
//        )
//    ),
//
//    'buttons'=>array(
//        'login'=>array(
//            'type'=>'submit',
//            'label'=>'Login',
//        ),
//    ),
//), $model);
?>