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
    public $verifyCode;

    public function rules()
    {
        $rules = [
            ['username, password, verifyPassword, email', 'required'],
            [
                'username',
                'length',
                'max'=>20,
                'min' => 3,
                'message' => Module::t("Incorrect username (length between 3 and 20 characters).")
            ],
            [
                'password',
                'length',
                'max'=>128,
                'min' => 4,
                'message' => Module::t("Incorrect password (minimal length 4 symbols).")
            ],
            ['email', 'email'],
            ['username', 'unique', 'message' => Module::t("This user's name already exists.")],
            ['email', 'unique', 'message' => Module::t("This user's email address already exists.")],
//            [
//                'verifyPassword',
//                'compare',
//                'compareAttribute'=>'password',
//                'message' => Module::t("Retype Password is incorrect.")
//            ],
            [
                'username',
                'match',
                'pattern' => '/^[A-Za-z0-9_]+$/u','message' => Module::t("Incorrect symbols (A-z0-9).")
            ],
        ];
        if (!(isset($_POST['ajax']) && $_POST['ajax']==='registration-form')) {
            array_push($rules, ['verifyCode', 'captcha', 'allowEmpty'=>!Module::doCaptcha('registration')]);
        }

        array_push(
            $rules,
            [
                'verifyPassword',
                'compare',
                'compareAttribute'=>'password',
                'message' => Module::t("Retype Password is incorrect.")
            ]
        );

        return $rules;
    }
}
