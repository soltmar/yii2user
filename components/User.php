<?php

namespace marsoltys\yii2user\components;

use marsoltys\yii2user\Module;
use Yii;

/**
 * @inheritdoc
 *
 * @property \marsoltys\yii2user\models\User|\yii\web\IdentityInterface|null $identity
 *      The identity object associated with the currently logged-in user.
 *      null is returned if the user is not logged in (not authenticated).
 *
 */

class User extends \yii\web\User
{

    /**
     * @var boolean whether to enable cookie-based login. Defaults to true.
     */
    public $enableAutoLogin = true;

    /**
     * @var string|array the URL for login. If using array, the first element should be
     * the route to the login action, and the rest name-value pairs are GET parameters
     * to construct the login URL (e.g. array('/site/login')). If this property is null,
     * a 403 HTTP exception will be raised instead.
     * @see CController::createUrl
     */
    public $loginUrl= ['/user/login'];

    private $sessionKeys = [];

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

    /**
     * Returns user model by user id.
     * @param integer $id user id. Default - current user id.
     * @return \marsoltys\yii2user\models\User
     */
    public function model($id = 0)
    {
        return $this->module->user($id);
    }

    /**
     * Returns user model by user id.
     * @param integer $id user id. Default - current user id.
     * @return \marsoltys\yii2user\models\User
     */
    public function user($id = 0)
    {
        return $this->model($id);
    }

    /**
     * Returns user model by user name.
     * @param string $username
     * @return \marsoltys\yii2user\models\User
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
     * This function is to make it compatible with new Yii2 flash messages
     * @param string $key
     * @param mixed $value
     * @param bool|true $del
     */
    public function setFlash($key, $value, $del = true)
    {
        Yii::$app->session->setFlash("user.".$key, $value, $del);
    }

    /**
     * This function is to make it compatible with new Yii2 flash messages
     * @param $key
     * @param string|null $defaultValue
     * @param bool|false $delete
     * @return mixed
     */
    public function getFlash($key, $defaultValue = null, $delete = false)
    {
        return Yii::$app->session->getFlash("user.".$key, $defaultValue, $delete);
    }

    /**
     * This function is to make it compatible with new Yii2 flash messages
     * @param $key
     * @return bool
     */
    public function hasFlash($key)
    {
        return Yii::$app->session->hasFlash("user.".$key);
    }

    /**
     * This function is to make it compatible with new Yii2 flash messages
     * @return array
     */
    public function getFlashes()
    {
        $messages = Yii::$app->session->getAllFlashes();

        $user_message = [];

        foreach ($messages as $key => $value) {
            if (strpos($key, 'user.') !== false) {
                $nkey = str_replace("user.", "", $key);
                $user_message[$nkey] = $value;
                unset($messages[$key]);
            }
        }
        return $user_message;
    }


    /**
     * Checks if current user is an Administrator
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->module->isAdmin();
    }

    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch (\Exception $e) {
            $val = \Yii::$app->session->get("user.$name", false);
            if (!$val) {
                throw $e;
            }

            return $val;
        }
    }

    public function __set($name, $value)
    {
        try {
            parent::__set($name, $value);
        } catch (\Exception $e) {
            \Yii::$app->session->set("user.$name", $value);
        }
    }

}
