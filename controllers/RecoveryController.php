<?php

namespace mariusz_soltys\yii2user\controllers;

use mariusz_soltys\yii2user\models\User;
use mariusz_soltys\yii2user\models\UserChangePassword;
use mariusz_soltys\yii2user\models\UserRecoveryForm;
use mariusz_soltys\yii2user\Module;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class RecoveryController extends Controller
{
    public $defaultAction = 'recovery';

    /**
     * Recovery password
     */
    public function actionRecovery()
    {
        /**@var Module $module*/
        $module = Yii::$app->controller->module;
        $form = new UserRecoveryForm;
        if (Yii::$app->user->id) {
            $this->redirect($module->returnUrl);
        } else {
            $email = ((isset($_GET['email']))?$_GET['email']:'');
            $activkey = ((isset($_GET['activkey']))?$_GET['activkey']:'');
            if ($email&&$activkey) {
                $form2 = new UserChangePassword;
                $find = User::find()->notsafe()->andWhere(['email'=>$email])->one();
                if (isset($find) && $find->activkey == $activkey) {
                    if (isset($_POST['UserChangePassword'])) {
                        $form2->load($_POST['UserChangePassword']);
                        if ($form2->validate()) {
                            $find->password = $module->encrypting($form2->password);
                            $find->activkey=$module->encrypting(microtime().$form2->password);
                            if ($find->status==0) {
                                $find->status = 1;
                            }
                            $find->save();
                            Yii::$app->user->setFlash('recoveryMessage', Module::t("New password is saved."));
                            $this->redirect($module->recoveryUrl);
                        }
                    }
                    $this->render('changepassword', ['form'=>$form2]);
                } else {
                    Yii::$app->user->setFlash('recoveryMessage', Module::t("Incorrect recovery link."));
                    $this->redirect($module->recoveryUrl);
                }
            } else {
                if (isset($_POST['UserRecoveryForm'])) {
                    $form->load($_POST['UserRecoveryForm']);
                    if ($form->validate()) {
                        $user = User::find()->notsafe()->findbyPk($form->user_id)->one();
                        $activation_url = 'http://' . $_SERVER['HTTP_HOST'].
                            Url::to(
                                implode($module->recoveryUrl),
                                ["activkey" => $user->activkey, "email" => $user->email]
                            );

                        $subject = Module::t(
                            "You have requested the password recovery site {site_name}",
                            [
                                '{site_name}'=>Yii::$app->name,
                            ]
                        );
                        $message = Module::t(
                            "You have requested the password recovery site {site_name}.
                            To receive a new password, go to {activation_url}.",
                            [
                                '{site_name}'=>Yii::$app->name,
                                '{activation_url}'=>$activation_url,
                            ]
                        );

                        Module::sendMail($user->email, $subject, $message);

                        Yii::$app->user->setFlash(
                            'recoveryMessage',
                            Module::t("Please check your email. An instructions was sent to your email address.")
                        );
                        $this->refresh();
                    }
                }
                $this->render('recovery', array('form'=>$form));
            }
        }
    }
}
