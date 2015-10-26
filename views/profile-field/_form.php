<?php

use mariusz_soltys\yii2user\controllers\ProfileFieldController;
use mariusz_soltys\yii2user\models\ProfileField;
use mariusz_soltys\yii2user\Module;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model mariusz_soltys\yii2user\models\ProfileField */

?>

<div class="form">

    <?php $form = ActiveForm::begin([
        'id'=>'profilefield-form',
        'enableAjaxValidation'=>true
    ]); ?>

    <p class="note"><?= Module::t('Fields with <span class="required">*</span> are required.'); ?></p>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'varname')->textInput(['size'=>60, 'maxlength'=>50, 'readonly'=>!empty($model->id)]); ?>
    <p class="hint-block"><?= Module::t("Allowed lowercase letters and digits."); ?></p>

    <?= $form->field($model, 'title')->textInput(['size'=>60, 'maxlength'=>255]); ?>
    <p class="hint"><?= Module::t('Field name on the language of "sourceLanguage".'); ?></p>

    <?php
    if ($model->id) {
        echo $form->field($model, 'field_type')->textInput([
            'size'=>60, 'maxlength'=>50, 'readonly'=>true, 'id'=>'field_type'
        ]);
    } else {
        echo $form->field($model, 'field_type')
            ->dropDownList(ProfileField::itemAlias('field_type'), ['id'=>'field_type']);
    } ?>
    <p class="hint"><?= Module::t('Field type column in the database.'); ?></p>

    <?= $form->field($model, 'field_size')->textInput(['readonly'=>empty($model->id)]); ?>
    <p class="hint"><?= Module::t('Field size column in the database.'); ?></p>

    <?= $form->field($model, 'field_size_min'); ?>
    <p class="hint"><?= Module::t('The minimum value of the field (form validator).'); ?></p>

    <?= $form->field($model, 'required')->dropDownList(ProfileField::itemAlias('required')); ?>
    <p class="hint"><?= Module::t('Required field (form validator).'); ?></p>

    <?= $form->field($model, 'match')->textInput(['size'=>60, 'maxlength'=>255]); ?>
    <p class="hint"><?= Module::t("Regular expression (example: '/^[A-Za-z0-9\s,]+$/u')."); ?></p>

    <?= $form->field($model, 'range')->textInput(['size'=>60, 'maxlength'=>5000]); ?>
    <p class="hint">
        <?= Module::t('Predefined values (example: 1;2;3;4;5 or 1==One;2==Two;3==Three;4==Four;5==Five).'); ?>
    </p>

    <?= $form->field($model, 'error_message')->textInput(['size'=>60, 'maxlength'=>255]); ?>
    <p class="hint"><?= Module::t('Error message when you validate the form.'); ?></p>

    <?= $form->field($model, 'other_validator')->textInput(['size'=>60, 'maxlength'=>255]); ?>
    <p class="hint">
        <?= Module::t('JSON string (example: {example}).', [
                '{example}'=> Json::encode(
                    ['file'=> ['types'=>'jpg, gif, png']]
                )]); ?>
    </p>

    <?= $form->field($model, 'default')->textInput(['size'=>60, 'maxlength'=>255, 'readonly'=>!empty($model->id)]); ?>
    <p class="hint"><?= Module::t('The value of the default field (database).'); ?></p>

    <?php list($widgetsList) = ProfileFieldController::getWidgets($model->field_type); ?>
    <?= $form->field($model, 'widget')->dropDownList($widgetsList, ['id' => 'widgetlist']); ?>
    <p class="hint"><?= Module::t('Widget name.'); ?></p>

    <?= $form->field($model, 'other_validator')->textInput(['size'=>60, 'maxlength'=>5000, 'id'=>'widgetparams']); ?>
    <p class="hint">
        <?= Module::t('JSON string (example: {example}).', [
                '{example}'=> Json::encode([
                    'param1'=> ['val1','val2'],
                    'param2'=> ['k1'=>'v1','k2'=>'v2']
                ])
            ]); ?>
    </p>

    <?= $form->field($model, 'position'); ?>
    <p class="hint"><?= Module::t('Display order of fields.'); ?></p>

    <?= $form->field($model, 'visible')->dropDownList(ProfileField::itemAlias('visible')); ?>

    <div class="form-group">
        <?=
        Html::submitButton(
            $model->isNewRecord ? Module::t('Create') : Module::t('Save'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div><!-- form -->
<div id="dialog-form" title="<?= Module::t('Widget parameters'); ?>">
    <form>
        <fieldset>
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
            <label for="value">Value</label>
            <input type="text" name="value" id="value" value="" class="text ui-widget-content ui-corner-all" />
        </fieldset>
    </form>
</div>
