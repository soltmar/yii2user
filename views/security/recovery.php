<?php

use marsoltys\yii2user\Module;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form marsoltys\yii2user\models\UserRecoveryForm */

$this->title=Yii::$app->name . ' - '.Module::t("Restore");
$this->params['breadcrumbs']= [
    ['label' => Module::t("Login"), 'url' => ['/user/login']],
    Module::t("Forgot your password?"),
];
?>

    <h1><?= Module::t("Forgot your password?"); ?></h1>

<?php if (Yii::$app->user->hasFlash('recoveryMessage')) : ?>
    <div class="alert alert-success" role="alert">
        <?= Yii::$app->user->getFlash('recoveryMessage'); ?>
    </div>
<?php else : ?>

    <div class="form">

        <div class="col-lg-4">
            <?php $f = ActiveForm::begin(['id'=>'recover-password-form']); ?>

            <?= $f->errorSummary($form); ?>

            <?= $f->field($form, 'login_or_email')->hint(Module::t("This can be your username or email address.")); ?>

            <div class="form-group">
                <?=
                Html::submitButton(
                    Module::t("Restore"),
                    ['class' => 'btn btn-primary']
                ); ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div><!-- form -->
<?php endif; ?>