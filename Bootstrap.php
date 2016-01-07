<?php
/**
 *
 * Project: Yii2User
 * Date: 07/01/2016
 * @author Mariusz Soltys.
 * @version 1.0.0
 * @license
 *
 */

namespace marsoltys\yii2user;


use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\web\Application $app
     */
    public function bootstrap($app)
    {
        $module = $app->getModule('user');
        $rules = $module->urlRules;
        $urlManager = $app->getUrlManager();
        //$urlManager->enablePrettyUrl = true;
        $urlManager->addRules($rules, true);

        $app->set('user', [
            'identityClass' => $module->identityClass,
            'loginUrl' => $module->loginUrl,
            'class' => $module->userClass
        ]);

    }
}
