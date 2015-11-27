<?php
/**
 *
 * Project: Yii2User
 * Date: 12/11/2015
 * @author Mariusz Soltys.
 * @version 1.0.0
 * @license http://opensource.org/licenses/MIT
 *
 */

/* @var $this \yii\web\View */
/* @var $content string */

use marsoltys\yii2user\assets\UserAssets;
use marsoltys\yii2user\Module;
use yii\web\View;

UserAssets::register($this);

$this->beginContent(Module::getInstance()->mainLayout);

$this->registerJS(
    '$(".flashes .alert").delay(3000).fadeOut("slow").slideUp("slow")',
    View::POS_READY,
    'HideEffect'
);
if (!Yii::$app->request->isAjax) {
    $flashMessages = Yii::$app->user->getFlashes();
    if ($flashMessages) {
        echo '<div class="flashes">';
        foreach ($flashMessages as $key => $message) {
            echo '<div class="alert alert-' . $key . '">' . $message . "</div>\n";
        }
        echo '</div>';
    }
}
?>

<?= $content ?>

<?php $this->endContent();
