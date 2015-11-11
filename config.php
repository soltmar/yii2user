<?php
/**
 *
 * Project: yii2user
 * Date: 08/11/2015
 * @author Mariusz Soltys.
 * @version 1.0.0
 * @license http://opensource.org/licenses/MIT
 *
 */

return [

    'mainLayout' => '@app/views/layouts/main.php',
    'layout' => "topmenu",
    'user_page_size' => 10,
    'fields_page_size' => 10,
    'emailFrom' => Yii::$app->params['adminEmail'],
    'sendActivationMail' => true,
    'loginNotActiv' => false,
    'activeAfterRegister' => false,
    'autoLogin' => true,
    'rememberMeTime' => 2592000, // 30 days
    'urlRules' => [
        'login' => 'user/security/login',
        'logout' => 'user/security/logout',
        [
            'class' => 'yii\web\GroupUrlRule',
            'prefix' => 'user',
            'rules' => [
                '/' => 'user/index',
                'registration' => 'registration/registration',
//                'login' => 'security/login',
//                'logout' => 'security/logout',
                'admin/view/<id:\d+>' => 'admin/view',
                'admin/update/<id:\d+>' => 'admin/update',
                'admin' => 'admin/admin',
                'profile-field/<id:\d+>' => 'profile-field/view',
            ],
        ]
    ],
];
