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

use mariusz_soltys\yii2user\models\UserChangePassword;
use mariusz_soltys\yii2user\models\UserRecoveryForm;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use mariusz_soltys\yii2user\models\User;
use mariusz_soltys\yii2user\models\UserLogin;
use mariusz_soltys\yii2user\Module;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

class SecurityController extends Controller
{
    public $defaultAction = 'login';

    /**@var \yii\web\Request */
    private $r;

    public $layout = "//main";

    public function init()
    {
        parent::init();
        $this->r = Yii::$app->request;
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            $this->redirect(Module::getInstance()->returnUrl);
        }
        /** @var $model UserLogin */
        $model=new UserLogin();
        // collect user input data
        if ($model->load($this->r->post()) && $model->validate()) {
            $this->lastVisit();
            if (Url::base()."/index.php" === Yii::$app->user->returnUrl) {
                return $this->redirect(Module::getInstance()->returnUrl);
            } else {
                return $this->redirect(Yii::$app->user->returnUrl);
            }
        }
        // display the login form
        return $this->render('login', ['model'=>$model]);
    }


    /**
     * Logout the current user and redirect to returnLogoutUrl.
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        $this->redirect(Module::getInstance()->returnLogoutUrl);
    }

    public function actionActivation()
    {
        $email = $this->r->get('email');
        $activkey = $this->r->get('activkey');
        $title = Module::t("User activation");
        $content = Module::t("Incorrect activation URL.");

        if ($email && $activkey) {
            $find = User::find()->notsafe()->andWhere(['email'=>$email])->one();

            if ($find  &&$find->status) {
                $content = Module::t("Your account is active.");
            } elseif (isset($find->activkey) && ($find->activkey==$activkey)) {
                $find->activkey = Module::encrypting(microtime());
                $find->status = 1;
                $find->save();
                $content = Module::t("Your account has been activated.");
            } else {
                $content = Module::t("Incorrect activation URL.");
            }
        }

        return $this->render('message', ['title'=>$title,  'content'=>$content]);
    }

    public function actionRecovery()
    {
        /**@var Module $module*/
        $module = Yii::$app->controller->module;
        $form = new UserRecoveryForm;
        if (Yii::$app->user->id) {
            $this->redirect($module->returnUrl);
        }

        $email = $this->r->get('email');
        $activkey = $this->r->get('activkey');
        if ($email&&$activkey) {
            $form2 = new UserChangePassword;
            $find = User::find()->notsafe()->andWhere(['email'=>$email])->one();
            if (isset($find) && $find->activkey == $activkey) {
                if ($form2->load(Yii::$app->request->post())) {
                    if ($form2->validate()) {
                        $find->password = $module->encrypting($form2->password);
                        $find->activkey=$module->encrypting(microtime().$form2->password);
                        if ($find->status==0) {
                            $find->status = 1;
                        }
                        $find->save();
                        Yii::$app->user->setFlash('success', Module::t("New password has been saved."));
                        return $this->redirect($module->loginUrl);
                    }
                }
                return $this->render('recoverpassword', ['form'=>$form2]);
            } else {
                Yii::$app->user->setFlash('danger', Module::t("Incorrect recovery link."));
                return $this->redirect($module->recoveryUrl);
            }
        }

        if ($form->load($this->r->post())) {
            if ($form->validate()) {
                $user = User::find()->notsafe()->findbyPk($form->user_id)->one();
                $url = Url::to(
                    array_merge(
                        $module->recoveryUrl,
                        [
                            "activkey" => $user->activkey,
                            "email" => $user->email
                        ]
                    ),
                    true
                );

                $activation_url = Html::a($url, $url);

                $subject = Module::t(
                    "{site_name} password recovery.",
                    [
                        'site_name'=>Yii::$app->name,
                    ]
                );

                $mail = Module::sendMail($user->email, $subject, 'recover', [
                    'activation_url' => $activation_url
                ]);

                if ($mail) {
                    Yii::$app->user->setFlash(
                        'success',
                        Module::t("Please check your email. An activation instructions was sent to your email address.")
                    );
                } else {
                    Yii::$app->user->setFlash(
                        'danger',
                        Module::t("Oops! There was a problem with sending activation email.")
                    );
                }

                return $this->refresh();
            }
        }
        return $this->render('recovery', array('form'=>$form));
    }

    /**
     * Change password
     */
    public function actionChangepassword()
    {
        ///**@var UserChangePassword $model*/
        $model = new UserChangePassword;

        if (Yii::$app->user->id) {
            if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }

            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate()) {
                    $new_password = User::find()->notsafe()->andWhere(['id'=>Yii::$app->user->id])->one();
                    $new_password->password = Module::encrypting($model->password);
                    $new_password->activkey=Module::encrypting(microtime().$model->password);
                    $new_password->save();
                    Yii::$app->user->setFlash('profileMessage', Module::t("New password is saved."));
                    $this->redirect(array("profile"));
                }
            }
            return $this->render('changepassword', ['model'=>$model]);
        }

        return $this->redirect(Module::getInstance()->loginUrl);
    }

    private function lastVisit()
    {
        /** @var $lastVisit User*/
        $lastVisit = User::findOne(Yii::$app->user->id);
        $lastVisit->lastvisit_at = date('Y-m-d H:i:s');
        $lastVisit->save();
    }
}
