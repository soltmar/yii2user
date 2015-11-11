<?php

use mariusz_soltys\yii2user\models\Profile;
use mariusz_soltys\yii2user\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \mariusz_soltys\yii2user\models\RegistrationForm */
/* @var $profile \mariusz_soltys\yii2user\models\Profile */

$this->title=Yii::$app->name . ' - '.Module::t("Registration");
$this->params['breadcrumbs'][] = Module::t("Registration");
?>

    <h1><?php echo Module::t("Registration"); ?></h1>

<?php if (Yii::$app->user->hasFlash('registration')) : ?>
    <div class="success">
        <?php echo Yii::$app->user->getFlash('registration'); ?>
    </div>
<?php else : ?>

    <div class="form">
        <div class="col-lg-5">

            <?php $form = ActiveForm::begin([
                'id'=>'registration-form',
                'enableClientValidation' => true,
            ]); ?>

            <p class="note"><?php echo Module::t('Fields with <span class="required">*</span> are required.'); ?></p>

            <?php echo $form->errorSummary(array($model,$profile)); ?>

            <?= $form->field($model, 'username'); ?>

            <?=
                $form->field($model, 'password')
                    ->passwordInput()
                    ->hint(Module::t("Minimal password length 4 symbols."));
            ?>

            <?= $form->field($model, 'verifyPassword')->passwordInput(); ?>

            <?= $form->field($model, 'email'); ?>

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

            <?php
            if (Module::doCaptcha('registration')) {
                echo $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::classname(), [

                    'captchaAction' => '/site/captcha',
                ])
                    ->hint(Module::t("Please enter the letters as they are shown in the image above.")
                        . "<br/>" . Module::t("Letters are not case-sensitive."));
            }
            ?>

            <div class="form-group">
                <?= Html::submitButton(Module::t('Register'), ['class' => 'btn btn-success']); ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

    </div><!-- form -->
<?php endif; ?>