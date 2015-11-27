<?php

    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;

    /**
     * @var $this yii\web\View
     * @var marsoltys\yii2user\models\User $model
     */

?>
<div class="wide form">
    <?php

        $form = ActiveForm::begin([
            'id' => 'login-form',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
        ]);
    ?>

    <?= $form->field($model, 'id'); ?>

    <?= $form->field($model, 'username')->textInput(['size'=>20, 'maxlength'=>20]);  ?>

    <?= $form->field($model, 'email')->textInput(['size'=>60,'maxlength'=>128]); ?>


    <?= $form->field($model, 'activkey')->textInput(['size'=>60, 'maxlength'=>128]); ?>

    <?= $form->field($model, 'create_at'); ?>

    <?= $form->field($model, 'lastvisit_at'); ?>

    <?= $form->field($model, 'superuser')->dropDownList($model->itemAlias('AdminStatus')); ?>

    <?= $form->field($model, 'status')->dropDownList($model->itemAlias('UserStatus')); ?>


    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton(\marsoltys\yii2user\Module::t('search'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end() ?>

</div><!-- search-form -->