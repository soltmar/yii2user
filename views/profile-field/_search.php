<?php
    use mariusz_soltys\yii2user\models\ProfileField;
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;

/**
* @var $this yii\web\View
* @var mariusz_soltys\yii2user\models\ProfileField $model
*/
?>
<div class="wide form">

    <?php
        $form = ActiveForm::begin([
            'id' => 'field-form',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
        ]);
    ?>

    <?= $form->field($model, 'id'); ?>

    <?= $form->field($model, 'varname')->textInput(['size'=>50, 'maxlength'=>50]); ?>

    <?= $form->field($model, 'title')->textInput(['size'=>60, 'maxlength'=>255]); ?>

    <?= $form->field($model, 'field_type')->dropDownList(ProfileField::itemAlias('field_type')); ?>

    <?= $form->field($model, 'field_size'); ?>

    <?= $form->field($model, 'field_size_min'); ?>

    <?= $form->field($model, 'required')->dropDownList(ProfileField::itemAlias('required')); ?>

    <?= $form->field($model, 'match')->textInput(['size'=>60, 'maxlength'=>255]); ?>

    <?= $form->field($model, 'range')->textInput(['size'=>60, 'maxlength'=>255]); ?>

    <?= $form->field($model, 'error_message')->textInput(['size'=>60, 'maxlength'=>255]); ?>

    <?= $form->field($model, 'other_validator')->textInput(['size'=>60, 'maxlength'=>5000]); ?>

    <?= $form->field($model, 'default')->textInput(['size'=>60, 'maxlength'=>255]); ?>

    <?= $form->field($model, 'widget')->textInput(['size'=>60, 'maxlength'=>255]); ?>

    <?= $form->field($model, 'widgetparams')->textInput(['size'=>60, 'maxlength'=>5000]); ?>

    <?= $form->field($model, 'position'); ?>

    <?= $form->field($model, 'visible')->dropDownList(ProfileField::itemAlias('visible')); ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton(\mariusz_soltys\yii2user\Module::t('search'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end() ?>

</div><!-- search-form --> 