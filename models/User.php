<?php

namespace marsoltys\yii2user\models;

use Yii;
use yii\db\ActiveRecord;

use marsoltys\yii2user\Module;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $activkey
 * @property integer $superuser
 * @property integer $status
 * @property string $create_at
 * @property string $lastvisit_at
 *
 * The followings are the available model relations:
 * @property Profile $profile
 *
 * @method ActiveRecord static findOne(mixed $condition)
 */
class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    const STATUS_NOACTIVE=0;
    const STATUS_ACTIVE=1;
    const STATUS_BANNED=-1;

    const ERROR_NONE=0;
    const ERROR_USERNAME_INVALID=1;
    const ERROR_PASSWORD_INVALID=2;
    const ERROR_EMAIL_INVALID=3;
    const ERROR_STATUS_NOTACTIV=4;
    const ERROR_STATUS_BAN=5;
    const ERROR_UNKNOWN_IDENTITY=100;

    const SCENARIO_SEARCH = 'search';
    const SCENARION_INSERT = 'insert';

    public $errorCode;

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return '{{users}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.CConsoleApplication
        return (
        (
            get_class(Yii::$app)=='yii\console\Application' ||
            (get_class(Yii::$app)!='yii\console\Application' && Yii::$app->user->isAdmin())
        )?[
            ['username', 'string', 'max'=>20, 'min' => 3,
                'message' => Module::t("Incorrect username (length between 3 and 20 characters).")],
            ['password', 'string', 'max'=>128, 'min' => 4,
                'message' => Module::t("Incorrect password (minimal length 4 symbols).")],
            ['email', 'email'],
            ['username', 'unique', 'message' => Module::t("This user's name already exists.")],
            ['email', 'unique', 'message' => Module::t("This user's email address already exists.")],
            ['username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u',
                'message' => Module::t("Incorrect symbols (A-z0-9).")],
            ['status', 'in', 'range'=>[self::STATUS_NOACTIVE,self::STATUS_ACTIVE,self::STATUS_BANNED]],
            ['superuser', 'in', 'range'=>[0,1]],
            ['create_at', 'default', 'value' => date('Y-m-d H:i:s'),
                'on' => self::SCENARION_INSERT],
            ['lastvisit_at', 'default', 'value' => '0000-00-00 00:00:00',
                'on' => self::SCENARION_INSERT],
            [['username', 'email', 'superuser', 'status'], 'required'],
            [['superuser', 'status'], 'integer'],
            [
                ['id', 'username', 'password', 'email', 'activkey', 'create_at', 'lastvisit_at', 'superuser', 'status'],
                'safe',
                'on' => self::SCENARIO_SEARCH
            ],
        ]:((Yii::$app->user->id==$this->id)?[
            [['username', 'email'], 'required'],
            ['username', 'string', 'max'=>20, 'min' => 3,
                'message' => Module::t("Incorrect username (length between 3 and 20 characters).")],
            ['email', 'email'],
            ['username', 'unique', 'message' => Module::t("This user's name already exists.")],
            ['username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u',
                'message' => Module::t("Incorrect symbols (A-z0-9).")],
            ['email', 'unique', 'message' => Module::t("This user's email address already exists.")],
        ]:[])
        );
    }

    /**
     * Relations
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t("Id"),
            'username'=>Module::t("Username"),
            'password'=>Module::t("Password"),
            'verifyPassword'=>Module::t("Retype Password"),
            'email'=>Module::t("E-mail"),
            'verifyCode'=>Module::t("Verification Code"),
            'activkey' => Module::t("Activation Key"),
            'createtime' => Module::t("Registration date"),
            'create_at' => Module::t("Registration date"),

            'lastvisit_at' => Module::t("Last visit"),
            'superuser' => Module::t("Superuser"),
            'status' => Module::t("Status"),
        ];
    }

    /**
     * @param $type
     * @param null $code
     * @return mixed
     */
    public static function itemAlias($type, $code = null)
    {
        $_items = [
            'UserStatus' => [
                self::STATUS_NOACTIVE => Module::t('Not active'),
                self::STATUS_ACTIVE => Module::t('Active'),
                self::STATUS_BANNED => Module::t('Banned'),
            ],
            'AdminStatus' => [
                '0' => Module::t('No'),
                '1' => Module::t('Yes'),
            ],
        ];
        if (isset($code)) {
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        } else {
            return isset($_items[$type]) ? $_items[$type] : false;
        }
    }

    public function getCreatetime()
    {
        return strtotime($this->create_at);
    }

    public function setCreatetime($value)
    {
        $this->create_at = date('Y-m-d H:i:s', $value);
    }

    public function getLastvisit()
    {
        return strtotime($this->lastvisit_at);
    }

    public function setLastvisit($value)
    {
        $this->lastvisit_at = date('Y-m-d H:i:s', $value);
    }

    public function afterSave($insert, $changedAttributes)
    {
        //TODO update user session if user is logged in
//        if (get_class(Yii::$app)=='CWebApplication'&&Profile::$regMode==false) {
//            Yii::$app->user->updateSession();
//        }
        return parent::afterSave($insert, $changedAttributes);
    }

    //TODO Do we actually need method below?
//
//    public function beforeSave($insert)
//    {
//        if (parent::beforeSave($insert)) {
//            if ($this->isNewRecord) {
//                $this->activkey = \Yii::$app->security->generateRandomString();
//            }
//            return true;
//        }
//        return false;
//    }

    /**
     * Returns User model if authenticated or false if not
     * @param string $username entered through login form
     * @param string $password entered through login form
     * @return User
     * @throws \yii\base\InvalidConfigException
     */
    public function authenticate($username, $password)
    {
        $user = $this::find()->notsafe()->filterWhere([
            'or',
            ['username' => $username],
            ['email' => $username]
        ])->one();

        var_dump($this->status);
        $t = Yii::$app->getSecurity()->validatePassword($password, $user->password);
        if (!$user) {
            $user->errorCode = self::ERROR_USERNAME_INVALID;
        } elseif (!Yii::$app->getSecurity()->validatePassword($password, $user->password)) {
            $user->errorCode = self::ERROR_PASSWORD_INVALID;
        } elseif ($user->status == self::STATUS_NOACTIVE && Module::getInstance()->loginNotActiv == false) {
            $user->errorCode = self::ERROR_STATUS_NOTACTIV;
        } elseif ($user->status == self::ERROR_STATUS_BAN) {
            $user->errorCode = self::ERROR_STATUS_BAN;
        } else {
            $user->errorCode = self::ERROR_NONE;
        }
        return $user;
    }

    /**
     * Finds an identity by the given ID.
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]]
     * will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|integer an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->activkey;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return boolean whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
}
