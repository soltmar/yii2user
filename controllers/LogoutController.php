<?php

namespace mariusz_soltys\yii2user\controllers;

class LogoutController extends Controller
{
	public $defaultAction = 'logout';
	
	/**
	 * Logout the current user and redirect to returnLogoutUrl.
	 */
	public function actionLogout()
	{
		Yii::$app->user->logout();
		$this->redirect(Yii::$app->controller->module->returnLogoutUrl);
	}

}