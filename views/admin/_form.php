<?php

use mariusz_soltys\yii2user\models\Profile;
use mariusz_soltys\yii2user\models\User;
use mariusz_soltys\yii2user\UserModule;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model mariusz_soltys\yii2user\models\User */
/* @var $profile mariusz_soltys\yii2user\models\Profile */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form">


    <?php $form = ActiveForm::begin([
        'id'=>'user-form',
        'enableAjaxValidation'=>true,
        'options' => ['enctype'=>'multipart/form-data'],
    ]); ?>

    <p class="note"><?php echo UserModule::t('Fields with <span class="required">*</span> are required.'); ?></p>

    <?php echo $form->errorSummary(array($model,$profile)); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'superuser')->dropDownList(User::itemAlias('AdminStatus')) ?>

    <?= $form->field($model, 'status')->dropDownList(User::itemAlias('UserStatus')) ?>

    <?php
    $profileFields=Profile::getFields();
    if ($profileFields) {
        foreach ($profileFields as $field) {
            /**@var \mariusz_soltys\yii2user\models\ProfileField $field*/
            $input = $form->field($model, $field->varname);

            if ($widgetEdit = $field->widgetEdit($profile)) {
                echo $widgetEdit;
            } elseif ($field->range) {
                echo $input->dropDownList(Profile::range($field->range));
            } elseif ($field->field_type=="TEXT") {
                echo $input->textarea(['rows'=>6, 'cols'=>50]);
            } else {
                echo $input->textInput(['size'=>60,'maxlength'=>(($field->field_size)?$field->field_size:255)]);
            }
        }
    }
    ?>
    <div class="form-group">
        <?=
        Html::submitButton(
            $model->isNewRecord ? UserModule::t('Create') : UserModule::t('Save'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div><!-- form -->