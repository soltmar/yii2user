<?php

namespace mariusz_soltys\yii2user\components;

use mariusz_soltys\yii2user\models\User;
use mariusz_soltys\yii2user\UserModule;
use Yii;
use yii\helpers\ArrayHelper;


/**
 * @inheritdoc
 *
 * @property \app\models\User|\yii\web\IdentityInterface|null $identity The identity object associated with the currently logged-in user. null is returned if the user is not logged in (not authenticated).
 */

class WebUser extends \yii\web\User
{

    /**
     * @var boolean whether to enable cookie-based login. Defaults to false.
     */
    public $allowAutoLogin=true;
    /**
     * @var string|array the URL for login. If using array, the first element should be
     * the route to the login action, and the rest name-value pairs are GET parameters
     * to construct the login URL (e.g. array('/site/login')). If this property is null,
     * a 403 HTTP exception will be raised instead.
     * @see CController::createUrl
     */
    public $loginUrl=array('/user/login');

    private $_sessionKeys = array();

    /** @var UserModule $module */
    private $module;

    function init() {
        parent::init();
        $this->module = Yii::$app->getModule('user');
    }

    public function getRole()
    {
        return Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());;
    }
    
    public function getId()
    {
        return $this->id ? $this->id : 0;
    }

//    protected function beforeLogin($id, $states, $fromCookie)
//    {
//        parent::beforeLogin($id, $states, $fromCookie);
//
//        $model = new UserLoginStats();
//        $model->attributes = array(
//            'user_id' => $id,
//            'ip' => ip2long(Yii::$app->request->getUserHostAddress())
//        );
//        $model->save();
//
//        return true;
//    }

    protected function afterLogin($identity, $cookieBased, $duration)
	{
        parent::afterLogin($identity, $cookieBased, $duration);
       // $this->updateSession();

        //TODO Check what user components properties needs to be stored in session
	}

//    public function updateSession() {
//        if ($user = Yii::$app->getModule('user')->user($this->id)) {
//            $this->name = $user->username;
//            $this = ArrayHelper::merge(array(
//                'email'=>$user->email,
//                'username'=>$user->username,
//                'create_at'=>$user->create_at,
//                'lastvisit_at'=>$user->lastvisit_at,
//            ),$user->profile->getAttributes());
//            foreach ($userAttributes as $attrName=>$attrValue) {
//                $this->setState($attrName,$attrValue);
//            }
//        }
//    }

    /**
     * Returns user model by user id.
     * @param integer $id user id. Default - current user id.
     * @return User
     */
    public function model($id=0) {
        return $this->module->user($id);
    }

    /**
     * Returns user model by user id.
     * @param integer $id user id. Default - current user id.
     * @return User
     */
    public function user($id=0) {
        return $this->model($id);
    }

    /**
     * Returns user model by user name.
     * @param string $username
     * @return User
     */
    public function getUserByName($username) {
        return $this->module->getUserByName($username);
    }

    public function getAdmins() {
        return $this->module->getAdmins();
    }


    /**
     * @return boolean
     */
    public function isAdmin() {
        return $this->module->isAdmin();
    }

    public function __get($name){
        try{
            return parent::__get($name);
        }catch (\yii\base\UnknownPropertyException $e){
            if(in_array($name, $this->_sessionKeys)){
                return \Yii::$app->session->get("user.$name");
            }else{
                throw $e;
            }
        }
    }

    public function __set($name, $value){
        try {
            parent::__set($name, $value);
        }catch (\yii\base\UnknownPropertyException $e){
            if(in_array($name, $this->_sessionKeys)){
                \Yii::$app->session->set("user.$name", $value);
            }else{
                throw $e;
            }
        }
    }

}
