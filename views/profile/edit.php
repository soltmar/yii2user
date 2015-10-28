<?php

    use mariusz_soltys\yii2user\models\Profile;
    use mariusz_soltys\yii2user\Module;
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;

    /* @var $this yii\web\View */
    /* @var $model mariusz_soltys\yii2user\models\User */
    /* @var $profile mariusz_soltys\yii2user\models\Profile */

    $this->title=Yii::$app->name . ' - '.Module::t("Profile");
    $this->params['breadcrumbs']= [
        ['label' => Module::t("Profile"), 'url' => ['profile']],
        Module::t("Edit"),
    ];

    $menu = [
        ['label'=>Module::t('List User'), 'url'=> ['/user']],
        ['label'=>Module::t('Profile'), 'url'=> ['/user/profile']],
        ['label'=>Module::t('Change password'), 'url'=> ['changepassword']],
        ['label'=>Module::t('Logout'), 'url'=> ['/user/logout']],
    ];

    if (Module::isAdmin()) {
        array_unshift($menu, ['label'=>Module::t('Manage Users'), 'url'=> ['/user/admin']]);
    }

    Module::getInstance()->setMenu($menu);

?>
<h1><?= Module::t('Edit profile'); ?></h1>

<?php if (Yii::$app->user->hasFlash('profileMessage')) : ?>
    <div class="success">
        <?= Yii::$app->user->getFlash('profileMessage'); ?>
    </div>
<?php endif; ?>
<div class="form">

    <?php $form = ActiveForm::begin([
        'id'=>'profile-form',
        'enableAjaxValidation'=>true,
        'options' => ['enctype'=>'multipart/form-data'],
    ]); ?>

    <p class="note"><?= Module::t('Fields with <span class="required">*</span> are required.'); ?></p>

    <?= $form->errorSummary(array($model, $profile)); ?>

    <?php
    $profileFields=Profile::getFields();
    if ($profileFields) {
        foreach ($profileFields as $field) {
            /**@var \mariusz_soltys\yii2user\models\ProfileField $field*/
            $input = $form->field($profile, $field->varname);

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

    <?= $form->field($model, 'username'); ?>

    <?= $form->field($model, 'email'); ?>

    <div class="form-group">
        <?=
            Html::submitButton(
                $model->isNewRecord ? Module::t('Create') : Module::t('Save'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
            ); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div><!-- form -->
