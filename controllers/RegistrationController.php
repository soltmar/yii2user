<?php

namespace marsoltys\yii2user\controllers;

use marsoltys\yii2user\models\Profile;
use marsoltys\yii2user\models\RegistrationForm;
use marsoltys\yii2user\models\User;
use marsoltys\yii2user\Module;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;

class RegistrationController extends Controller
{
    public $defaultAction = 'registration';

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
            $this->redirect($module->profileUrl);
        } else {
            if ($model->load(Yii::$app->request->post())) {
                $profile->load(Yii::$app->request->post());
                if ($model->validate()&&$profile->validate()) {
                    $model->activkey=Module::encrypting(microtime().$model->password);

                    $model->superuser=0;
                    $model->status=$module->activeAfterRegister?User::STATUS_ACTIVE:User::STATUS_NOACTIVE;

                    if ($model->save(false)) {
                        $profile->user_id=$model->id;
                        $profile->save(false);
                        if ($module->sendActivationMail) {
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
                                    "{site_name} account activation",
                                    ['site_name'=>Yii::$app->name]
                                ),
                                'register',
                                [
                                    'activation_url' => $activation_url
                                ]

                            );
                        }

                        if (
                            (
                                $module->loginNotActiv ||(
                                    $module->activeAfterRegister &&
                                    $module->sendActivationMail==false
                                )
                            ) && $module->autoLogin
                        ) {
                            Yii::$app->user->login($model);
                            $this->redirect($module->returnUrl);
                        } else {
                            if (
                                !$module->activeAfterRegister &&
                                !$module->sendActivationMail
                            ) {
                                Yii::$app->user->setFlash(
                                    'success',
                                    Module::t(
                                        "Thank you for your registration. Contact Admin to activate your account."
                                    )
                                );
                            } elseif (
                                $module->activeAfterRegister &&
                                $module->sendActivationMail == false
                            ) {
                                Yii::$app->user->setFlash(
                                    'success',
                                    Module::t(
                                        "Thank you for your registration. Please {{login}}.",
                                        [
                                            '{{login}}'=>Html::a(
                                                Module::t('Login'),
                                                $module->loginUrl
                                            )
                                        ]
                                    )
                                );
                            } elseif ($module->loginNotActiv) {
                                Yii::$app->user->setFlash(
                                    'success',
                                    Module::t("Thank you for your registration. Please check your email or login.")
                                );
                            } else {
                                Yii::$app->user->setFlash(
                                    'success',
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
