<?php

namespace mariusz_soltys\yii2user\controllers;

use mariusz_soltys\yii2user\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

class DefaultController extends Controller
{

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

        $this->render('/user/index', [
            'dataProvider'=>$provider,
        ]);
    }

}