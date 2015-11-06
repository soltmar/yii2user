<?php
/**
 *
 * Project: Yii2User
 * Date: 06/11/2015
 * @author Mariusz Soltys.
 * @version 1.0.0
 * @license http://opensource.org/licenses/MIT
 *
 */

namespace mariusz_soltys\yii2user\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use mariusz_soltys\yii2user\models\User;
use mariusz_soltys\yii2user\models\UserLogin;
use mariusz_soltys\yii2user\Module;

class SecurityController extends Controller
{
    public $defaultAction = 'login';

    /**
     * Logout the current user and redirect to returnLogoutUrl.
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        $this->redirect(Module::getInstance()->returnLogoutUrl);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            $this->redirect(Module::getInstance()->returnUrl);
        }
        /** @var $model UserLogin */
        $model=new UserLogin();
        // collect user input data
        if (isset($_POST['UserLogin'])) {
            $model->load(Yii::$app->request->post());
            // validate user input and redirect to previous page if valid
            if ($model->validate()) {
                $this->lastVisit();
                if (Url::base()."/index.php" === Yii::$app->user->returnUrl) {
                    $this->redirect(Module::getInstance()->returnUrl);
                } else {
                    $this->redirect(Yii::$app->user->returnUrl);
                }
            }
        }
        // display the login form
        return $this->render('/user/login', ['model'=>$model]);
    }

    private function lastVisit()
    {
        /** @var $lastVisit User*/
        $lastVisit = User::findOne(Yii::$app->user->id);
        $lastVisit->lastvisit_at = date('Y-m-d H:i:s');
        $lastVisit->save();
    }
}