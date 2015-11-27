<?php
namespace marsoltys\yii2user;

use yii\base\BootstrapInterface;

/**
 *
 * Project: Yii2User
 * Date: 11/11/2015
 * @author Mariusz Soltys.
 * @version 1.0.0
 * @license http://opensource.org/licenses/MIT
 *
 */
class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $rules = \Yii::$app->getModule('user')->urlRules;
        $app->getUrlManager()->addRules($rules, true);
    }
}