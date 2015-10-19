<?php

namespace mariusz_soltys\yii2user\controllers;

use mariusz_soltys\yii2user\models\Profile;
use mariusz_soltys\yii2user\models\User;
use mariusz_soltys\yii2user\UserModule;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\widgets\ActiveForm;

class AdminController extends Controller
{
    public $defaultAction = 'admin';
    public $layout='//layouts/column2';

    private $model;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['admin','delete','create','update','view'],
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback'=>function () {
                            return Yii::$app->user->isAdmin();
                        },
                    ],
                ],
            ],
        ];
    }


    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model=new User;
        $model->scenario = $model::SCENARIO_SEARCH;  // clear any default values

        if (isset($_GET['User'])) {
            $model->load($_GET['User']);
        }

        $this->render('index', array(
            'model'=>$model,
        ));
        /*$dataProvider=new CActiveDataProvider('User', array(
            'pagination'=>array(
                'pageSize'=>Yii::$app->controller->module->user_page_size,
            ),
        ));

        $this->render('index',array(
            'dataProvider'=>$dataProvider,
        ));//*/
    }


    /**
     * Displays a particular model.
     */
    public function actionView()
    {
        $model = $this->loadModel();
        $this->render('view', [
            'model'=>$model,
        ]);
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model=new User;
        $profile=new Profile;
        $this->performAjaxValidation([$model,$profile]);
        if (isset($_POST['User'])) {
            $model->load($_POST['User']);
            $model->activkey=Yii::$app->controller->module->encrypting(microtime().$model->password);
            $profile->load($_POST['Profile']);
            $profile->user_id=0;
            if ($model->validate()&&$profile->validate()) {
                $model->password=Yii::$app->controller->module->encrypting($model->password);
                if ($model->save()) {
                    $profile->user_id=$model->id;
                    $profile->save();
                }
                $this->redirect(array('view','id'=>$model->id));
            } else {
                $profile->validate();
            }
        }

        $this->render('create', array(
            'model'=>$model,
            'profile'=>$profile,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     */
    public function actionUpdate()
    {
        $model=$this->loadModel();
        $profile=$model->profile;
        $this->performAjaxValidation([$model, $profile]);
        if (isset($_POST['User'])) {
            $model->attributes=$_POST['User'];
            $profile->attributes=$_POST['Profile'];

            if ($model->validate()&&$profile->validate()) {
                $old_password = User::findOne($model->id)->notsafe();
                if ($old_password->password!=$model->password) {
                    $model->password=Yii::$app->controller->module->encrypting($model->password);
                    $model->activkey=Yii::$app->controller->module->encrypting(microtime().$model->password);
                }
                $model->save();
                $profile->save();
                $this->redirect(array('view','id'=>$model->id));
            } else {
                $profile->validate();
            }
        }

        $this->render('update', [
            'model'=>$model,
            'profile'=>$profile,
        ]);
    }


    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     */
    public function actionDelete()
    {
        if (Yii::$app->request->isPostRequest) {
            // we only allow deletion via POST request
            $model = $this->loadModel();
            /** @var Profile $profile */
            $profile = Profile::findOne($model->id);

            // Make sure profile exists
            if ($profile) {
                $profile->delete();
            }

            $model->delete();
            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_POST['ajax'])) {
                $this->redirect(array('/user/admin'));
            }
        } else {
            throw new HttpException(400, 'Invalid request. Please do not repeat this request again.');
        }
    }

    /**
     * Performs the AJAX validation.
     * @param $validate array the model to be validated
     */
    protected function performAjaxValidation($validate)
    {
        if (isset($_POST['ajax']) && $_POST['ajax']==='user-form') {
            echo ActiveForm::validateMultiple($validate);
            Yii::$app->end();
        }
    }


    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @return User
     * @throws HttpException
     */
    public function loadModel()
    {
        if ($this->model===null) {
            if (isset($_GET['id'])) {
                $this->model = User::findOne($_GET['id'])->notsafe();
            }
            if ($this->model===null) {
                throw new HttpException(404, 'The requested page does not exist.');
            }
        }
        return $this->model;
    }
}
