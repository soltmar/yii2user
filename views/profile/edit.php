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

    <?= $form->errorSummary([$model, $profile]); ?>

    <?php
    $profileFields=Profile::getFields();
    if ($profileFields) {
        foreach ($profileFields as $field) {
            $field->renderField($profile, $form);
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
