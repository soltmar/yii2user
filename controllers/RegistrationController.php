<?php

namespace mariusz_soltys\yii2user\controllers;

use mariusz_soltys\yii2user\models\Profile;
use mariusz_soltys\yii2user\models\RegistrationForm;
use mariusz_soltys\yii2user\models\User;
use mariusz_soltys\yii2user\UserModule;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\widgets\ActiveForm;

class RegistrationController extends Controller
{
    public $defaultAction = 'registration';

    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            'captcha'=>Yii::$app->getModule('user')->captchaParams,
        );
    }
    /**
     * Registration user
     */
    public function actionRegistration()
    {
        Profile::$regMode = true;
        $model = new RegistrationForm;
        $profile=new Profile;

        // ajax validator
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post()) && $profile->load(Yii::$app->request->post())) {
                echo ActiveForm::validateMultiple([$model, $profile]);
                Yii::$app->end();
            }
        }

        if (Yii::$app->user->id) {
            $this->redirect(Yii::$app->controller->module->profileUrl);
        } else {
            if (isset($_POST['RegistrationForm'])) {
                $model->load($_POST['RegistrationForm']);
                $profile->load(isset($_POST['Profile'])?$_POST['Profile']:array());
                if ($model->validate()&&$profile->validate()) {
                    $model->activkey=UserModule::encrypting(microtime().$model->password);
                    $model->password=UserModule::encrypting($model->password);
                    $model->verifyPassword=UserModule::encrypting($model->verifyPassword);
                    $model->superuser=0;
                    $model->status=(
                        (Yii::$app->controller->module->activeAfterRegister)?User::STATUS_ACTIVE:User::STATUS_NOACTIVE
                    );

                    if ($model->save()) {
                        $profile->user_id=$model->id;
                        $profile->save();
                        if (Yii::$app->controller->module->sendActivationMail) {
                            $activation_url = Url::to(
                                '/user/activation/activation',
                                ["activkey" => $model->activkey, "email" => $model->email]
                            );
                            UserModule::sendMail(
                                $model->email,
                                UserModule::t(
                                    "You registered from {site_name}",
                                    ['{site_name}'=>Yii::$app->name]
                                ),
                                UserModule::t(
                                    "Please activate you account go to {activation_url}",
                                    ['{activation_url}'=>$activation_url]
                                )
                            );
                        }

                        if (
                            (
                                Yii::$app->controller->module->loginNotActiv ||(
                                    Yii::$app->controller->module->activeAfterRegister &&
                                    Yii::$app->controller->module->sendActivationMail==false
                                )
                            ) && Yii::$app->controller->module->autoLogin
                        ) {
                            Yii::$app->user->login($model);
                            $this->redirect(Yii::$app->controller->module->returnUrl);
                        } else {
                            if (
                                !Yii::$app->controller->module->activeAfterRegister &&
                                !Yii::$app->controller->module->sendActivationMail
                            ) {
                                Yii::$app->user->setFlash(
                                    'registration',
                                    UserModule::t(
                                        "Thank you for your registration. Contact Admin to activate your account."
                                    )
                                );
                            } elseif (
                                Yii::$app->controller->module->activeAfterRegister &&
                                Yii::$app->controller->module->sendActivationMail == false
                            ) {
                                Yii::$app->user->setFlash(
                                    'registration',
                                    UserModule::t(
                                        "Thank you for your registration. Please {{login}}.",
                                        [
                                            '{{login}}'=>Html::a(
                                                UserModule::t('Login'),
                                                Yii::$app->controller->module->loginUrl
                                            )
                                        ]
                                    )
                                );
                            } elseif (Yii::$app->controller->module->loginNotActiv) {
                                Yii::$app->user->setFlash(
                                    'registration',
                                    UserModule::t("Thank you for your registration. Please check your email or login.")
                                );
                            } else {
                                Yii::$app->user->setFlash(
                                    'registration',
                                    UserModule::t("Thank you for your registration. Please check your email.")
                                );
                            }
                            $this->refresh();
                        }
                    }
                } else {
                    $profile->validate();
                }
            }
            $this->render('/user/registration', ['model'=>$model,'profile'=>$profile]);
        }
    }
}
