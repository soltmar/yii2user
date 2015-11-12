<?php

namespace mariusz_soltys\yii2user\models;

use mariusz_soltys\yii2user\Module;

/**
 * RegistrationForm class.
 * RegistrationForm is the data structure for keeping
 * user registration form data. It is used by the 'registration' action of 'UserController'.
 */
class RegistrationForm extends User
{
    public $verifyPassword;
    public $captcha;

    public function rules()
    {

        $rules = [
            [
                'captcha',
                'captcha', 'captchaAction'=>'/site/captcha'],
            [['username', 'password', 'verifyPassword', 'email'], 'required'],
            [['username', 'password', 'verifyPassword', 'email'], 'safe'],
            [
                'username',
                'string',
                'max'=>20,
                'min' => 3,
                'message' => Module::t("Incorrect username (length between 3 and 20 characters).")
            ],
            [
                'password',
                'string',
                'max'=>128,
                'min' => 4,
                'message' => Module::t("Incorrect password (minimal length 4 symbols).")
            ],
            ['email', 'email'],
            ['username', 'unique', 'message' => Module::t("This user's name already exists.")],
            ['email', 'unique', 'message' => Module::t("This user's email address already exists.")],
            [
                'verifyPassword',
                'compare',
                'compareAttribute'=>'password',
                'message' => Module::t("Retype Password is incorrect.")
            ],
            [
                'username',
                'match',
                'pattern' => '/^[A-Za-z0-9_]+$/u','message' => Module::t("Incorrect symbols (A-z0-9).")
            ],
        ];

        return $rules;
    }

    /** Encrypt password before saving to database */
    public function beforeSave($insert)
    {
        $this->password = Module::encrypting($this->password);
        return parent::beforeSave($insert);
    }
}
