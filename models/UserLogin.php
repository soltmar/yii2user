<?php

namespace mariusz_soltys\yii2user\models;

use mariusz_soltys\yii2user\Module;
use Yii;

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class UserLogin extends \yii\base\Model
{
    public $username;
    public $password;
    public $rememberMe;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            // username and password are required
            array(['username', 'password'], 'required'),
            // rememberMe needs to be a boolean
            array('rememberMe', 'boolean'),
            // password needs to be authenticated
            array('password', 'authenticate'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'rememberMe'=>Module::t("Remember me"),
            'username'=>Module::t("Username"),
            'password'=>Module::t("Password"),
        );
    }

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate()
    {
        if (!$this->hasErrors()) {
            $user = new User;
            $identity = $user->authenticate($this->username, $this->password);
            switch ($identity->errorCode) {
                case User::ERROR_NONE:
                    $duration=$this->rememberMe ? Module::getInstance()->rememberMeTime : 0;
                    Yii::$app->user->login($identity, $duration);
                    break;
                case User::ERROR_EMAIL_INVALID:
                    $this->addError("username", Module::t("Email is incorrect."));
                    break;
                case User::ERROR_USERNAME_INVALID:
                    $this->addError("username", Module::t("Username is incorrect."));
                    break;
                case User::ERROR_STATUS_NOTACTIV:
                    $this->addError("status", Module::t("Your account is not activated."));
                    break;
                case User::ERROR_STATUS_BAN:
                    $this->addError("status", Module::t("Your account is blocked."));
                    break;
                case User::ERROR_PASSWORD_INVALID:
                    $this->addError("password", Module::t("Password is incorrect."));
                    break;
            }
        }
    }
}
