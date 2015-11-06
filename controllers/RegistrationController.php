<?php

namespace mariusz_soltys\yii2user\controllers;

use mariusz_soltys\yii2user\models\Profile;
use mariusz_soltys\yii2user\models\RegistrationForm;
use mariusz_soltys\yii2user\models\User;
use mariusz_soltys\yii2user\Module;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

class RegistrationController extends Controller
{
    public $defaultAction = 'registration';

    /**
     * Declares class-based actions.
     */
//    public function actions()
//    {
//        return [
//            'captcha' => Module::getInstance()->captchaParams,
//        ];
//    }
    /**
     * Registration user
     */
    public function actionRegistration()
    {
        Profile::$regMode = true;
        $model = new RegistrationForm;
        $profile=new Profile;
        $module = Module::getInstance();

        // ajax validator
//        if (Yii::$app->request->isAjax) {
//            if ($model->load(Yii::$app->request->post()) && $profile->load(Yii::$app->request->post())) {
//                Yii::$app->response->format = Response::FORMAT_JSON;
//                return ActiveForm::validateMultiple([$model, $profile]);
//            }
//        }

        if (Yii::$app->user->id) {
            $this->redirect(Yii::$app->controller->module->profileUrl);
        } else {
            if ($model->load(Yii::$app->request->post())) {
                $profile->load(Yii::$app->request->post());
                if ($model->validate()&&$profile->validate()) {
                    $model->activkey=Module::encrypting(microtime().$model->password);
                   // $model->password=Module::encrypting($model->password);
                   // $model->verifyPassword=Module::encrypting($model->verifyPassword);

                    $model->superuser=0;
                    $model->status=(
                        (Yii::$app->controller->module->activeAfterRegister)?User::STATUS_ACTIVE:User::STATUS_NOACTIVE
                    );

                    if ($model->save(false)) {
                        $profile->user_id=$model->id;
                        $profile->save(false);
                        if (Yii::$app->controller->module->sendActivationMail) {
                            $url = Url::to(
                                array_merge(
                                    $module->activationUrl,
                                    [
                                        "activkey" => $model->activkey,
                                        "email" => $model->email
                                    ]
                                ),
                                true
                            );
                            $activation_url = Html::a($url, $url);
                            Module::sendMail(
                                $model->email,
                                Module::t(
                                    "You registered from {site_name}",
                                    ['site_name'=>Yii::$app->name]
                                ),
                                Module::t(
                                    "Please activate you account go to <a href='{activation_url}'>{activation_url}</a>",
                                    ['activation_url'=>$activation_url]
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
                                    Module::t(
                                        "Thank you for your registration. Contact Admin to activate your account."
                                    )
                                );
                            } elseif (
                                Yii::$app->controller->module->activeAfterRegister &&
                                Yii::$app->controller->module->sendActivationMail == false
                            ) {
                                Yii::$app->user->setFlash(
                                    'registration',
                                    Module::t(
                                        "Thank you for your registration. Please {{login}}.",
                                        [
                                            '{{login}}'=>Html::a(
                                                Module::t('Login'),
                                                Yii::$app->controller->module->loginUrl
                                            )
                                        ]
                                    )
                                );
                            } elseif (Yii::$app->controller->module->loginNotActiv) {
                                Yii::$app->user->setFlash(
                                    'registration',
                                    Module::t("Thank you for your registration. Please check your email or login.")
                                );
                            } else {
                                Yii::$app->user->setFlash(
                                    'registration',
                                    Module::t("Thank you for your registration. Please check your email.")
                                );
                            }
                            return $this->refresh();
                        }
                    }
                } else {
                    $profile->validate();
                }
            }
            return $this->render('/user/registration', ['model'=>$model,'profile'=>$profile]);
        }
    }
}
