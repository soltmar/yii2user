<?php

namespace mariusz_soltys\yii2user\controllers;

use mariusz_soltys\yii2user\models\User;
use mariusz_soltys\yii2user\models\UserChangePassword;
use mariusz_soltys\yii2user\UserModule;
use Yii;
use yii\web\Controller;
use yii\widgets\ActiveForm;

class ProfileController extends Controller
{
    public $defaultAction = 'profile';
    public $layout='//layouts/column2';

    /** @var \mariusz_soltys\yii2user\models\Profile the currently loaded data model instance. */
    private $model;
    /**
     * Shows a particular model.
     */
    public function actionProfile()
    {
        $model = $this->loadUser();
        $this->render('profile', [
            'model'=>$model,
            'profile'=>$model->profile,
        ]);
    }


    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     */
    public function actionEdit()
    {
        $model = $this->loadUser();
        $profile=$model->profile;

        // ajax validator
        if (isset($_POST['ajax']) && $_POST['ajax']==='profile-form') {
            echo ActiveForm::validateMultiple([$model,$profile]);
            Yii::$app->end();
        }

        if (isset($_POST['User'])) {
            $model->load($_POST['User']);
            $profile->load($_POST['Profile']);

            if ($model->validate()&&$profile->validate()) {
                $model->save();
                $profile->save();
                Yii::$app->user->setFlash('profileMessage', UserModule::t("Changes is saved."));
                $this->redirect(array('/user/profile'));
            } else {
                $profile->validate();
            }
        }

        $this->render('edit', [
            'model'=>$model,
            'profile'=>$profile,
        ]);
    }

    /**
     * Change password
     */
    public function actionChangepassword()
    {
        ///**@var UserChangePassword $model*/
        $model = new UserChangePassword;

        if (Yii::$app->user->id) {
            // ajax validator
            if (isset($_POST['ajax']) && $_POST['ajax']==='changepassword-form') {
                echo ActiveForm::validate($model);
                Yii::$app->end();
            }

            if (isset($_POST['UserChangePassword'])) {
                $model->load($_POST['UserChangePassword']);
                if ($model->validate()) {
                    $new_password = User::find()->notsafe()->andWhere(['id'=>Yii::$app->user->id])->one();
                    $new_password->password = UserModule::encrypting($model->password);
                    $new_password->activkey=UserModule::encrypting(microtime().$model->password);
                    $new_password->save();
                    Yii::$app->user->setFlash('profileMessage', UserModule::t("New password is saved."));
                    $this->redirect(array("profile"));
                }
            }
            $this->render('changepassword', ['model'=>$model]);
        }
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @return \mariusz_soltys\yii2user\models\User
     */
    public function loadUser()
    {
        if ($this->model===null) {
            if (Yii::$app->user->id) {
                $this->model = Yii::$app->controller->module->user();
            }
            if ($this->model===null) {
                $this->redirect(Yii::$app->controller->module->loginUrl);
            }
        }
        return $this->model;
    }
}