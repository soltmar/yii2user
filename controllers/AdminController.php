<?php

namespace mariusz_soltys\yii2user\controllers;

use mariusz_soltys\yii2user\models\Profile;
use mariusz_soltys\yii2user\models\search\UserSearch;
use mariusz_soltys\yii2user\models\User;
use mariusz_soltys\yii2user\Module;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class AdminController extends Controller
{
    public $defaultAction = 'admin';

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
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', array(
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
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
        return $this->render('view', [
            'model'=>$model,
        ]);
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new User;
        $profile = new Profile;
        $this->performAjaxValidation([$model,$profile]);
        if ($model->load(Yii::$app->request->post())) {
            $model->activkey = Module::getInstance()->encrypting(microtime().$model->password);
            $profile->load(Yii::$app->request->post());
            $profile->user_id=0;
            if ($model->validate()&&$profile->validate()) {
                $model->password = Module::getInstance()->encrypting($model->password);
                if ($model->save()) {
                    $profile->user_id = $model->id;
                    $profile->save();
                }
                $this->redirect(['view', 'id'=>$model->id]);
            } else {
                $profile->validate();
            }
        }

        return $this->render('create', [
            'model'=>$model,
            'profile'=>$profile,
        ]);
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
        if ($model->load(Yii::$app->request->post())) {
            $profile->load(Yii::$app->request->post());

            if ($model->validate()&&$profile->validate()) {
                $old_password = User::find()->notsafe()->findbyPk($model->id)->one();
                if ($old_password->password != $model->password) {
                    $model->password = Module::getInstance()->encrypting($model->password);
                    $model->activkey = Module::getInstance()->encrypting(microtime().$model->password);
                }
                $model->save();
                $profile->save();
                $this->redirect(['view', 'id'=>$model->id]);
            } else {
                $profile->validate();
            }
        }

        return $this->render('update', [
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
        if (Yii::$app->request->isPost) {
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
            if (!Yii::$app->request->isAjax) {
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
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validateMultiple($validate);
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
                $this->model = User::find()->notsafe()->findbyPk($_GET['id'])->one();
            }
            if ($this->model===null) {
                throw new HttpException(404, 'The requested page does not exist.');
            }
        }
        return $this->model;
    }
}
