<?php

use mariusz_soltys\yii2user\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form mariusz_soltys\yii2user\models\UserRecoveryForm */

$this->title=Yii::$app->name . ' - '.Module::t("Restore");
$this->params['breadcrumbs']= [
    ['label' => Module::t("Login"), 'url' => ['/user/login']],
    Module::t("Restore"),
];
?>

    <h1><?php echo Module::t("Restore"); ?></h1>

<?php if (Yii::$app->user->hasFlash('recoveryMessage')) : ?>
    <div class="alert alert-success" role="alert">
        <?php echo Yii::$app->user->getFlash('recoveryMessage'); ?>
    </div>
<?php else : ?>

    <div class="form">
        <?php echo Html::beginForm(); ?>

        <?php echo Html::errorSummary($form); ?>

        <div class="row">
            <?php echo Html::activeLabel($form, 'login_or_email'); ?>
            <?php echo Html::activeTextInput($form, 'login_or_email') ?>
            <p class="hint"><?php echo Module::t("Please enter your login or email address."); ?></p>
        </div>

        <div class="row submit">
            <?php echo Html::submitButton(Module::t("Restore")); ?>
        </div>

        <?php echo Html::endForm(); ?>
    </div><!-- form -->
<?php endif; ?>