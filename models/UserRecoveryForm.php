<?php

namespace mariusz_soltys\yii2user\models;

use mariusz_soltys\yii2user\Module;
use yii\base\Model;

/**
 * UserRecoveryForm class.
 * UserRecoveryForm is the data structure for keeping
 * user recovery form data. It is used by the 'recovery' action of 'UserController'.
 */
class UserRecoveryForm extends Model
{
    public $login_or_email;
    public $user_id;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            // username and password are required
            array('login_or_email', 'required'),
            array('login_or_email', 'match', 'pattern' => '/^[A-Za-z0-9@.-\s,]+$/u','message' => Module::t("Incorrect symbols (A-z0-9).")),
            // password needs to be authenticated
            array('login_or_email', 'checkexists'),
        );
    }
    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'login_or_email'=>Module::t("username or email"),
        );
    }

    public function checkexists()
    {
        if (!$this->hasErrors()) {// we only want to authenticate when no input errors
            /**@var User $user*/
            if (strpos($this->login_or_email, "@")) {
                $user=User::findOne(['email'=>$this->login_or_email]);
                if ($user) {
                    $this->user_id=$user->id;
                }
            } else {
                $user=User::findOne(['username'=>$this->login_or_email]);
                if ($user) {
                    $this->user_id=$user->id;
                }
            }

            if ($user === null) {
                if (strpos($this->login_or_email, "@")) {
                    $this->addError("login_or_email", Module::t("Email is incorrect."));
                } else {
                    $this->addError("login_or_email", Module::t("Username is incorrect."));
                }
            }
        }
    }

}