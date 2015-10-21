<?php

namespace mariusz_soltys\yii2user\controllers;

use mariusz_soltys\yii2user\models\User;
use mariusz_soltys\yii2user\models\UserLogin;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class LoginController extends Controller
{
    public $defaultAction = 'login';

    public function init()
    {
        parent::init();
    }

    public function actionIndex()
    {
        echo "Yoooo";
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        if (Yii::$app->user->isGuest) {
            /** @var $model UserLogin */
            $model=new UserLogin();
            // collect user input data
            if (isset($_POST['UserLogin'])) {
                $model->load(Yii::$app->request->post());
                // validate user input and redirect to previous page if valid
                if ($model->validate()) {
                    $this->lastVisit();
                    if (Url::base()."/index.php" === Yii::$app->user->returnUrl) {
                        $this->redirect(Yii::$app->controller->module->returnUrl);
                    } else {
                        $this->redirect(Yii::$app->user->returnUrl);
                    }
                }
            }
            // display the login form
            return $this->render('/user/login', ['model'=>$model]);
        } else {
            $this->redirect(Yii::$app->controller->module->returnUrl);
        }
    }

    private function lastVisit()
    {
        /** @var $lastVisit User*/
        $lastVisit = User::findOne(Yii::$app->user->id);
        $lastVisit->lastvisit_at = date('Y-m-d H:i:s');
        $lastVisit->save();
    }

}