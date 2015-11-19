<?php

use mariusz_soltys\yii2user\models\Profile;
use mariusz_soltys\yii2user\models\User;
use mariusz_soltys\yii2user\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model mariusz_soltys\yii2user\models\User */
/* @var $profile mariusz_soltys\yii2user\models\Profile */
?>

<div class="form">

    <div class="col-lg-4">

        <?php $form = ActiveForm::begin([
            'id'=>'user-form',
            'enableAjaxValidation'=>true,
            'options' => ['enctype'=>'multipart/form-data'],
        ]); ?>

        <p class="note"><?php echo Module::t('Fields with <span class="required">*</span> are required.'); ?></p>

        <?= $form->errorSummary([$model,$profile]); ?>

        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'superuser')->dropDownList(User::itemAlias('AdminStatus')) ?>

        <?= $form->field($model, 'status')->dropDownList(User::itemAlias('UserStatus')) ?>

        <?php
        $profileFields=Profile::getFields();
        if ($profileFields) {
            foreach ($profileFields as $field) {
                echo $field->renderField($profile, $form);
            }
        }
        ?>
        <div class="form-group">
            <?=
            Html::submitButton(
                $model->isNewRecord ? Module::t('Create') : Module::t('Save'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
            ); ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div><!-- form -->