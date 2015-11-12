<?php

use mariusz_soltys\yii2user\controllers\ProfileFieldController;
use mariusz_soltys\yii2user\models\ProfileField;
use mariusz_soltys\yii2user\Module;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model mariusz_soltys\yii2user\models\ProfileField */

?>

<div class="form">

    <div class="col-lg-5">

        <?php $form = ActiveForm::begin([
            'id'=>'profilefield-form',
            //'enableAjaxValidation'=>true
        ]); ?>

        <p class="note"><?= Module::t('Fields with <span class="required">*</span> are required.'); ?></p>

        <?= $form->errorSummary($model); ?>

        <?= $form->field($model, 'varname', ['options' => ['class'=>'form-group varname']])
            ->textInput(['size'=>60, 'maxlength'=>50, 'readonly'=>!empty($model->id)])
            ->hint(Module::t("Allowed lowercase letters and digits.")); ?>

        <?= $form->field($model, 'title', ['options' => ['class'=>'form-group title']])
            ->textInput(['size'=>60, 'maxlength'=>255])
            ->hint(Module::t('Field name on the language of "sourceLanguage".')); ?>

        <?php
        if ($model->id) {
            echo $form->field($model, 'field_type', ['options' => ['class'=>'form-group field_type']])
                ->textInput(['size'=>60, 'maxlength'=>50, 'readonly'=>true, 'id'=>'field_type'])
                ->hint(Module::t('Field type column in the database.'));
        } else {
            echo $form->field($model, 'field_type', ['options' => ['class'=>'form-group field_type']])
                ->dropDownList(ProfileField::itemAlias('field_type'), ['id'=>'field_type'])
                ->hint(Module::t('Field type column in the database.'));
        } ?>

        <?= $form->field($model, 'field_size', ['options' => ['class'=>'form-group field_size']])
            ->textInput(['readonly'=>empty($model->id)])
            ->hint(Module::t('Field size column in the database.')); ?>

        <?= $form->field($model, 'field_size_min', ['options' => ['class'=>'form-group field_size_min']])
            ->hint(Module::t('The minimum value of the field (form validator).')); ?>

        <?= $form->field($model, 'required', ['options' => ['class'=>'form-group required']])
            ->dropDownList(ProfileField::itemAlias('required'))
            ->hint(Module::t('Required field (form validator).')); ?>

        <?= $form->field($model, 'match', ['options' => ['class'=>'form-group match']])
            ->textInput(['size'=>60, 'maxlength'=>255])
            ->hint(Module::t("Regular expression (example: '/^[A-Za-z0-9\s,]+$/u').")); ?>

        <?= $form->field($model, 'range', ['options' => ['class'=>'form-group range']])
            ->textInput(['size'=>60, 'maxlength'=>5000])
            ->hint(Module::t('Predefined values (example: 1;2;3;4;5 or 1==One;2==Two;3==Three;4==Four;5==Five).')); ?>

        <?= $form->field($model, 'error_message', ['options' => ['class'=>'form-group error_message']])
            ->textInput(['size'=>60, 'maxlength'=>255])
            ->hint(Module::t('Error message when you validate the form.')); ?>

        <?= $form->field($model, 'other_validator', ['options' => ['class'=>'form-group other_validator']])
            ->textInput(['size'=>60, 'maxlength'=>255])
            ->hint(Module::t('JSON string (example: {example}).', [
                '{example}'=> Json::encode(
                    ['file'=> ['types'=>'jpg, gif, png']]
                )])); ?>

        <?= $form->field($model, 'default', ['options' => ['class'=>'form-group default']])
            ->textInput(['size'=>60, 'maxlength'=>255, 'readonly'=>!empty($model->id)])
            ->hint(Module::t('The value of the default field (database).')); ?>

        <?php list($widgetsList) = ProfileFieldController::getWidgets($model->field_type); ?>
        <?= $form->field($model, 'widget', ['options' => ['class'=>'form-group widget']])
            ->dropDownList($widgetsList, ['id' => 'widgetlist'])
            ->hint(Module::t('Widget name.')); ?>

        <?= $form->field($model, 'widgetparams', ['options' => ['class'=>'form-group widgetparams']])
            ->textInput(['size'=>60, 'maxlength'=>5000, 'id'=>'widgetparams'])
            ->hint(Module::t('JSON string (example: {example}).', [
                '{example}'=> Json::encode([
                    'param1'=> ['val1','val2'],
                    'param2'=> ['k1'=>'v1','k2'=>'v2']
                ])
            ])); ?>

        <?= $form->field($model, 'position', ['options' => ['class'=>'form-group position']])
            ->hint(Module::t('Display order of fields.')); ?>

        <?= $form->field($model, 'visible', ['options' => ['class'=>'form-group visible']])
            ->dropDownList(ProfileField::itemAlias('visible')); ?>

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
<div id="dialog-form" title="<?= Module::t('Widgets parameters'); ?>">
    <form>
        <fieldset>
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
            <label for="value">Value</label>
            <input type="text" name="value" id="value" value="" class="text ui-widget-content ui-corner-all" />
        </fieldset>
    </form>
</div>
