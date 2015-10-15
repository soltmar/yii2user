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

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		if (Yii::$app->user->isGuest) {
			$model=new UserLogin;
			// collect user input data
			if(isset($_POST['UserLogin']))
			{
				$model->attributes=$_POST['UserLogin'];
				// validate user input and redirect to previous page if valid
				if($model->validate()) {
					$this->lastVisit();
					if (Url::base()."/index.php" === Yii::$app->user->returnUrl)
						$this->redirect(Yii::$app->controller->module->returnUrl);
					else
						$this->redirect(Yii::$app->user->returnUrl);
				}
			}
			// display the login form
			$this->render('/user/login',array('model'=>$model));
		} else
			$this->redirect(Yii::$app->controller->module->returnUrl);
	}
	
	private function lastVisit() {
		$lastVisit = User::findOne(Yii::$app->user->id);
		$lastVisit->lastvisit_at = date('Y-m-d H:i:s');
		$lastVisit->save();
	}

}