<?php

use mariusz_soltys\yii2user\Module;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var mariusz_soltys\yii2user\models\UserLogin $model
 * @var $this yii\web\View
*/

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode(Module::t($this->title)) ?></h1>

<?php if (Yii::$app->user->hasFlash('status')) : ?>

<div class="success">
	<?= Yii::$app->user->getFlash('status'); ?>
</div>

<?php endif; ?>
<div class="site-login">


    <p><?= Module::t("Please fill out the following form with your login credentials:"); ?></p>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-6\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        ],
    ]); ?>

    <!--
	<p class="note"><?/*= Module::t('Fields with <span class="required">*</span> are required.'); */?></p>
    -->

	<?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-11">
		<?= Html::a(Module::t("Register"), Module::getInstance()->registrationUrl); ?>
			|
		<?= Html::a(Module::t("Lost Password?"), Module::getInstance()->recoveryUrl); ?>
		</div>
	</div>

    <?= $form->field($model, 'rememberMe')->checkbox([
        'template' => "<div class=\"col-lg-offset-2 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
    ]) ?>

    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-11">
            <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
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