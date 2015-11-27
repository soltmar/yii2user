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

use marsoltys\yii2user\Module;

/* @var $this \yii\web\View view component instance */
/* @var $activation_url string containing activation link HTML */
?>

<?= Module::t(
    "To activate your account follow this link: {activation_url}",
    ['activation_url'=>$activation_url]
);
