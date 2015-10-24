<?php

namespace mariusz_soltys\yii2user;

use mariusz_soltys\yii2user\models\User;
use Yii;

class Module extends \yii\base\Module
{
    public $mainLayout = '@app/views/layouts/main.php';
    /**
     * @var int
     * @desc items on page
     */
    public $user_page_size = 10;

    /**
     * @var int
     * @desc items on page
     */
    public $fields_page_size = 10;

    /**
     * @var string
     * @desc hash method (md5,sha1 or algo hash function http://www.php.net/manual/en/function.hash.php)
     */
    public $hash='md5';

    /**
     * @var boolean
     * @desc use email for activation user account
     */
    public $sendActivationMail=true;

    /**
     * @var boolean
     * @desc allow auth for is not active user
     */
    public $loginNotActiv = false;

    /**
     * @var boolean
     * @desc activate user on registration (only $sendActivationMail = false)
     */
    public $activeAfterRegister=false;

    /**
     * @var boolean
     * @desc login after registration (need loginNotActiv or activeAfterRegister = true)
     */
    public $autoLogin=true;

    public $registrationUrl = array("/user/registration");
    public $recoveryUrl = array("/user/recovery/recovery");
    public $loginUrl = array("/user/login");
    public $logoutUrl = array("/user/logout");
    public $profileUrl = array("/user/profile");
    public $returnUrl = array("/user/profile");
    public $returnLogoutUrl = array("/user/login");

    public $captchaParams = array(
        'class'=>'CCaptchaAction',
        'backColor'=>0xFFFFFF,
        'foreColor'=>0x2040A0,
    );


    /**
     * @var int
     * @desc Remember Me Time (seconds), defalt = 2592000 (30 days)
     */
    public $rememberMeTime = 2592000; // 30 days

    public $fieldsMessage = '';

    /**
     * @var array
     * @desc Profile model relation from other models
     */
    public $profileRelations = array();

    /**
     * @var array
     */
    public $captcha = array('registration'=>true);

    /**
     * @var boolean
     */
    //public $cacheEnable = false;

    public $tableUsers = '{{users}}';
    public $tableProfiles = '{{profiles}}';
    public $tableProfileFields = '{{profiles_fields}}';

    public $defaultScope = array(
        'with'=>array('profile'),
    );

    static private $user;
    static private $users=array();
    static private $userByName=array();
    static private $admin;
    static private $admins;

    /**
     * @var array
     * @desc Behaviors for models
     */
    public $componentBehaviors=array();

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'mariusz_soltys\yii2user\controllers';

    /**
     * @return Module
    */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    public function init()
    {
        parent::init();
        $this->setAliases([
            '@user-assets' => __DIR__ . '/views/assets',
        ]);
    }

    public function getBehaviorsFor($componentName)
    {
        if (isset($this->componentBehaviors[$componentName])) {
            return $this->componentBehaviors[$componentName];
        } else {
            return array();
        }
    }

//    public function beforeAction($action)
//    {
//        if (!parent::beforeAction($action)) {
//            return false;
//        }
//        // your custom code here
//        //
//        // return true; // or false to not run the action
//    }


    /**
     * @param $str
     * @param $params
     * @param $dic
     * @return string
     */
    public static function t($str = '', $params = array(), $dic = 'user')
    {
        if (Yii::t("user", $str)==$str) {
            return Yii::t("user.".$dic, $str, $params);
        } else {
            return Yii::t("user", $str, $params);
        }
    }

    /**
     * @param string $string string to encrypt
     * @return string hash.
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public static function encrypting($string = "")
    {

        return Yii::$app->getSecurity()->generatePasswordHash($string);
    }

    /**
     * @param $place
     * @return boolean
     */
    public static function doCaptcha($place = '')
    {
        if (!extension_loaded('gd')) {
            return false;
        }

        if (in_array($place, Module::getInstance()->captcha)) {
            return Module::getInstance()->captcha[$place];
        }

        return false;
    }

    /**
     * Return admin status.
     * @return boolean
     */
    public static function isAdmin()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        } else {
            if (!isset(self::$admin)) {
                if (self::user()->superuser) {
                    self::$admin = true;
                } else {
                    self::$admin = false;
                }
            }
            return self::$admin;
        }
    }

    /**
     * Return admins.
     * @return array superusers names
     */
    public static function getAdmins()
    {
        if (!self::$admins) {
            $admins = User::find()->active()->superuser()->all();
            $return_name = array();
            foreach ($admins as $admin) {
                array_push($return_name, $admin->username);
            }
            self::$admins = ($return_name)?$return_name:array('');
        }
        return self::$admins;
    }

    /**
     * Send to user mail
     * @param $email
     * @param $subject
     * @param $message
     * @return bool
     */
    public static function sendMail($email, $subject, $message)
    {
        $adminEmail = Yii::$app->params['adminEmail'];
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: $adminEmail\r\n";
        $headers .= "Reply-To: $adminEmail\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8";
        $message = wordwrap($message, 70);
        $message = str_replace("\n.", "\n..", $message);
        return mail($email, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers);
    }

    /**
     * Send to user mail
     * @param $user_id
     * @param $subject
     * @param $message
     * @param string $from
     * @return bool
     */
    public function sendMailToUser($user_id, $subject, $message, $from = '')
    {
        /**@var User $user*/
        $user = User::findOne($user_id);
        if (!$from) {
            $from = Yii::$app->params['adminEmail'];
        }
        $headers="From: ".$from."\r\nReply-To: ".Yii::$app->params['adminEmail'];
        return mail($user->email, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers);
    }

    /**
     * Return safe user data.
     * @param $id int user id not required
     * @param bool $clearCache
     * @return User object or false
     */
    public static function user($id = 0, $clearCache = false)
    {
        if (!$id&&!Yii::$app->user->isGuest) {
            $id = Yii::$app->user->id;
        }
        if ($id) {
            if (!isset(self::$users[$id])||$clearCache) {
                self::$users[$id] = User::findOne($id);
            }
            return self::$users[$id];
        } else {
            return false;
        }
    }

    /**
     * Return safe user data.
     * @param $username string user name
     * @return user object or false
     */
    public static function getUserByName($username)
    {
        $_userByName = [];
        if (!isset(self::$userByName[$username])) {
            $_userByName[$username] = User::findOne(['username'=>$username]);
        }
        return $_userByName[$username];
    }

//	/**
//	 * Return safe user data.
//	 * @param user id not required
//	 * @return user object or false
//	 */
//	public function users() {
//		return User;
//	}
}
