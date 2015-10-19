<?php

namespace mariusz_soltys\yii2user\models;

use mariusz_soltys\yii2user\Module;
use Yii;
use yii\base\Model;

/**
 * UserChangePassword class.
 * UserChangePassword is the data structure for keeping
 * user change password form data. It is used by the 'changepassword' action of 'UserController'.
 */
class UserChangePassword extends Model
{
    public $oldPassword;
    public $password;
    public $verifyPassword;

    public function rules()
    {
        return Yii::$app->controller->id == 'recovery' ? [
            ['password, verifyPassword', 'required'],
            ['password, verifyPassword', 'length', 'max'=>128, 'min' => 4,'message' => Module::t("Incorrect password (minimal length 4 symbols).")],
            ['verifyPassword', 'compare', 'compareAttribute'=>'password', 'message' => Module::t("Retype Password is incorrect.")],
        ] : [
            ['oldPassword, password, verifyPassword', 'required'],
            ['oldPassword, password, verifyPassword', 'length', 'max'=>128, 'min' => 4,'message' => Module::t("Incorrect password (minimal length 4 symbols).")],
            ['verifyPassword', 'compare', 'compareAttribute'=>'password', 'message' => Module::t("Retype Password is incorrect.")],
            ['oldPassword', 'verifyOldPassword'],
        ];
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'oldPassword'=>Module::t("Old Password"),
            'password'=>Module::t("password"),
            'verifyPassword'=>Module::t("Retype Password"),
        );
    }

    /**
     * Verify Old Password
     */
    public function verifyOldPassword($attribute, $params)
    {
        $cond = User::find()->notsafe()->findByPk(Yii::$app->user->id)->one()->password
            != Yii::$app->getModule('user')->encrypting($this->$attribute);
        if ($cond) {
            $this->addError($attribute, Module::t("Old Password is incorrect."));
        }
    }
}
