<?php

namespace mariusz_soltys\yii2user;

use mariusz_soltys\yii2user\models\User;
use Yii;
use yii\base\BootstrapInterface;
use yii\swiftmailer\Mailer;
use yii\web\GroupUrlRule;

class Module extends \yii\base\Module implements BootstrapInterface
{
    const ALERT_ERROR = 'danger';
    const ALERT_INFO = 'info';
    const ALERT_WARNING = 'warning';
    const ALERT_SUCCESS = 'success';

    public $mainLayout = '@app/views/layouts/main.php';

    public $mailViews = '@mariusz_soltys/yii2user/mail';

    public $urlPrefix = 'user';

    /** @var array The rules to be used in URL management. */
    public $urlRules = ['class' => 'yii\web\GroupUrlRule',
                        'prefix' => 'user',
                        'rules' => [
                            'login' => 'user/security/login',
                            'logout' => 'user/security/logout',
                        ],
    ];
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
     * @var string
     * @desc Email address present in "From" field
     */
    public $emailFrom;

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

    public $registrationUrl = ["/user/registration/registration"];
    public $recoveryUrl = ["/user/security/recovery"];
    public $activationUrl = ["/user/security/activation"];
    public $loginUrl = ["/user/security/login"];
    public $logoutUrl = ["/user/security/logout"];
    public $profileUrl = ["/user/profile"];
    public $returnUrl = ["/user/profile"];
    public $returnLogoutUrl = ["/user/security/login"];

    public $captchaParams = [
        'class'=>'yii\captcha\CaptchaAction',
        'backColor'=>0xFFFFFF,
        'foreColor'=>0x2040A0,
    ];

    private $menu = [];

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
    public $profileRelations = [];

    /**
     * @var array
     */
    public $captcha = ['registration' =>true];

    /**
     * @var boolean
     */
    //public $cacheEnable = false;

    public $tableUsers = '{{users}}';
    public $tableProfiles = '{{profiles}}';
    public $tableProfileFields = '{{profiles_fields}}';

    public $defaultScope = [
        'with'=> ['profile'],
    ];

    static private $user;
    static private $users= [];
    static private $userByName= [];
    static private $admin;
    static private $admins;

    /**
     * @var array
     * @desc Behaviors for models
     */
    public $componentBehaviors= [];

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'mariusz_soltys\yii2user\controllers';

    /**
     * @inheritdoc
     */
    public $defaultRoute = 'user';

    public function bootstrap($app)
    {
        $rules = $this->urlRules;
        $app->getUrlManager()->addRules($rules, true);
    }

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

        Yii::configure($this, require(__DIR__ . '/config.php'));
    }

    public function getBehaviorsFor($componentName)
    {
        if (isset($this->componentBehaviors[$componentName])) {
            return $this->componentBehaviors[$componentName];
        } else {
            return [];
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
    public static function t($str = '', $params = [], $dic = 'user')
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
            $return_name = [];
            foreach ($admins as $admin) {
                array_push($return_name, $admin->username);
            }
            self::$admins = ($return_name)?$return_name: [''];
        }
        return self::$admins;
    }

    /**
     * Send emails to specified $email address
     * @param string $email
     * @param string $subject
     * @param string $view
     * @param array $params
     * @return bool
     */
    public static function sendMail($email, $subject, $view, $params = [])
    {
        if (empty($params['from'])) {
            $params['from'] = Yii::$app->params['adminEmail'];
        }
        /** @var  $mailer Mailer*/
        $mailer = Yii::$app->mailer;
        $view = Module::getInstance()->mailViews."/".$view;
        $mailer->compose($view, $params)
            ->setFrom($params['from'])
            ->setTo($email)
            ->setSubject($subject)
            ->send();
        return $mailer;
    }

    /**
     * Send email to user based on user id
     * @param int $user_id
     * @param string $subject
     * @param string $view
     * @param array $params
     * @return bool
     */
    public function sendMailToUser($user_id, $subject, $view, $params = [])
    {
        /**@var User $user*/
        $user = User::findOne($user_id);
        return $this->sendMail($user->email, $subject, $view, $params);
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

    public function initMenu()
    {
        if (Module::isAdmin()) {
            $this->menu = [
                ['label' => Module::t('Create User'), 'url' => ['/user/admin/create']],
                ['label' => Module::t('Manage Users'), 'url' => ['/user/admin/admin']],
                ['label' => Module::t('Manage Profile Field'), 'url' => ['/user/profile-field/admin']],
                ['label' => Module::t('List User'), 'url' => ['/user/user/index']],
            ];
        }
    }

    public function getMenu()
    {
        if (empty($this->menu)) {
            $this->initMenu();
        }

        return $this->menu;
    }

    /**
     * Position is counted starting from number 1 (not 0 as in arrays)
     * @param array $option
     * @param false|int $position
     */
    public function addMenu($option, $position = 0)
    {
        if (empty($this->menu)) {
            $this->initMenu();
        }

        if ($position !== 0) {
            $this->menu = array_splice($this->menu, $position-1, 0, $option);
        } else {
            $this->menu[] = $option;
        }
    }

    public function setMenu($options)
    {
        $this->menu = $options;
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
