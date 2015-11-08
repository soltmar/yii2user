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
    'layout' => "rightmenu",
    'user_page_size' => 10,
    'fields_page_size' => 10,
    'sendActivationMail' => true,
    'loginNotActiv' => false,
    'activeAfterRegister' => false,
    'autoLogin' => true,
    'rememberMeTime' => 2592000, // 30 days
];
