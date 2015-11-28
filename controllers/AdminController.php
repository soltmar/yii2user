<?php

namespace marsoltys\yii2user\controllers;

use marsoltys\yii2user\models\Profile;
use marsoltys\yii2user\models\search\UserSearch;
use marsoltys\yii2user\models\User;
use marsoltys\yii2user\Module;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
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

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
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
        $post = Yii::$app->request->post();
        if ($model->load($post) && $profile->load($post)) {
            $model->loadDefaultValues(true);
            $validation = $this->performAjaxValidation([$model, $profile]);
            if ($validation!==false) {
                return $validation;
            }
            $model->activkey = Yii::$app->security->generateRandomString();
            $profile->user_id=0;
            if ($model->validate()) {
                $model->password = Module::getInstance()->encrypting($model->password);
                if ($model->save()) {
                    $profile->user_id = $model->id;
                    $profile->loadDefaultValues(true);
                    if ($profile->save(true)) {
                        Yii::$app->user->setFlash(
                            'success',
                            Module::t(
                                "User has been created successfully."
                            )
                        );
                        return $this->redirect(['view', 'id'=>$model->id]);
                    }
                }
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
        $post = Yii::$app->request->post();

        if ($model->load($post) && $profile->load($post)) {
            $validation = $this->performAjaxValidation([$model, $profile]);
            if ($validation!==false) {
                return $validation;
            }

            if ($model->validate()&&$profile->save()) {
                $old_password = User::find()->notsafe()->findbyPk($model->id)->one();
                if ($old_password->password != $model->password) {
                    $model->password = Module::getInstance()->encrypting($model->password);
                    $model->activkey = Yii::$app->security->generateRandomString();
                }
                if ($model->save(true)) {
                    return $this->redirect(['view', 'id'=>$model->id]);
                }
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
            if (Yii::$app->request->isAjax) {
                return '';
            }

            return $this->redirect(['/user/admin']);

        } else {
            throw new HttpException(400, 'Invalid request. Please do not repeat this request again.');
        }
    }

    /**
     * Performs the AJAX validation.
     * @param $validate array|\yii\db\ActiveRecord the model to be validated
     * @return array containing validation results for each of the models fields
     */
    protected function performAjaxValidation($validate)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (is_array($validate)) {
                $results = [];
                foreach ($validate as $model) {
                    $results = array_merge($results, ActiveForm::validate($model));
                }
                return $results;
            }
            return ActiveForm::validate($validate);
        }

        return false;
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
            if (Yii::$app->request->get('id')) {
                $this->model = User::find()->notsafe()->findbyPk(Yii::$app->request->get('id'))->one();
            }
            if ($this->model===null) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
        return $this->model;
    }
}
