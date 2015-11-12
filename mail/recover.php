<?php
/**
 *
 * Project: Yii2User
 * Date: 09/11/2015
 * @author Mariusz Soltys.
 * @version 1.0.0
 * @license http://opensource.org/licenses/MIT
 *
 */
use mariusz_soltys\yii2user\Module;

/** @var string $activation_url */
?>

<?= Module::t(
    "You have requested the password recovery on {site_name} site.<br><br>
     To receive a new password, go to {activation_url}.",
    [
        'site_name'=>Yii::$app->name,
        'activation_url'=>$activation_url,
    ]
);
