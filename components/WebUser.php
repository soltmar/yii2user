<?php

namespace mariusz_soltys\yii2user\components;

use mariusz_soltys\yii2user\models\User;
use mariusz_soltys\yii2user\Module;
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

    private $sessionKeys = array();

    /** @var Module $module */
    private $module;

    public function init()
    {
        parent::init();
        $this->module = Yii::$app->getModule('user');
    }

    public function getRole()
    {
        return Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
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
    public function model($id = 0)
    {
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
    public function getUserByName($username)
    {
        return $this->module->getUserByName($username);
    }

    public function getAdmins()
    {
        return $this->module->getAdmins();
    }

    /**
     * This is function here is to make it compatible with new Yii2 flash messages
     * @param string $key
     * @param mixed $value
     * @param bool|true $del
     */
    public function setFlash($key, $value, $del = true)
    {
        Yii::$app->session->setFlash($key, $value, $del);
    }

    /**
     * This is function here is to make it compatible with new Yii2 flash messages
     * @param $key
     * @param string|null $defaultValue
     * @param bool|false $delete
     * @return mixed
     */
    public function getFlash($key, $defaultValue = null, $delete = false)
    {
        return Yii::$app->session->getFlash($key, $defaultValue, $delete);
    }

    /**
     * This is function here is to make it compatible with new Yii2 flash messages
     * @param $key
     * @return bool
     */
    public function hasFlash($key)
    {
        return Yii::$app->session->hasFlash($key);
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
            if(in_array($name, $this->sessionKeys)){
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
            if(in_array($name, $this->sessionKeys)){
                \Yii::$app->session->set("user.$name", $value);
            }else{
                throw $e;
            }
        }
    }

}
