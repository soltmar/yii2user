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

use Yii;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\web\Application $app
     */
    public function bootstrap($app)
    {
        /* @var $module \marsoltys\yii2user\Module */
        $module = Yii::$app->getModule("user");
        $urlManager = $app->getUrlManager();
        //$urlManager->enablePrettyUrl = true;
        $urlManager->addRules($module->urlRules, true);

        $app->set('user', [
            'identityClass' => $module->identityClass,
            'loginUrl' => $module->loginUrl,
            'class' => $module->userClass
        ]);

    }
}
