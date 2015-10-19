<?php

namespace mariusz_soltys\yii2user\controllers;

use mariusz_soltys\yii2user\models\User;
use mariusz_soltys\yii2user\Module;
use yii\web\Controller;

class ActivationController extends Controller
{
    public $defaultAction = 'activation';

    /**
     * Activation user account
     */
    public function actionActivation()
    {
        $email = $_GET['email'];
        $activkey = $_GET['activkey'];

        if ($email&&$activkey) {
            $find = User::find()->notsafe()->andWhere(['email'=>$email])->one();

            if (isset($find)&&$find->status) {
                $this->render(
                    '/user/message',
                    [
                        'title'=>Module::t("User activation"),
                        'content'=>Module::t("You account is active.")
                    ]
                );
            } elseif (isset($find->activkey) && ($find->activkey==$activkey)) {
                $find->activkey = Module::encrypting(microtime());
                $find->status = 1;
                $find->save();

                $this->render(
                    '/user/message',
                    [
                        'title'=>Module::t("User activation"),
                        'content'=>Module::t("You account is activated.")
                    ]
                );
            } else {
                $this->render(
                    '/user/message',
                    [
                        'title'=>Module::t("User activation"),
                        'content'=>Module::t("Incorrect activation URL.")
                    ]
                );
            }
        } else {
            $this->render(
                '/user/message',
                [
                    'title'=>Module::t("User activation"),
                    'content'=>Module::t("Incorrect activation URL.")
                ]
            );
        }
    }

}