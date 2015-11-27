<?php

namespace marsoltys\yii2user\controllers;

use marsoltys\yii2user\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{
    /**
    í * @var CActiveRecord the currently loaded data model instance.
     */
    private $model;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','view'],
                'rules' => [
                    [
                        'allow' => true,
                        //'roles' => ['?'],
                    ],
                ],
            ],
        ];
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
     * Lists all models.
     */
    public function actionIndex()
    {
        $query = User::find()->where('status > '.User::STATUS_BANNED);
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->controller->module->user_page_size,
            ],
        ]);

        return $this->render('index', [
            'dataProvider'=>$provider,
        ]);
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     */
    public function loadModel()
    {
        if ($this->model === null) {
            if (isset($_GET['id'])) {
                $this->model=User::findOne($_GET['id']);
            }
            if ($this->model === null) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
        return $this->model;
    }


    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the primary key value. Defaults to null, meaning using the 'id' GET variable
     * @return User
     * @throws NotFoundHttpException
     */
    public function loadUser($id = null)
    {
        if ($this->model===null) {
            if ($id!==null || Yii::$app->request->get('id')) {
                $this->model=User::findOne($id!==null ? $id : Yii::$app->request->get('id'));
            }
            if ($this->model===null) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
        return $this->model;
    }
}
