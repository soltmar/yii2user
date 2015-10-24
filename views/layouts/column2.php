<?php

/* @var $this \yii\web\View */
use mariusz_soltys\yii2user\Module;
use mariusz_soltys\yii2user\UserAssets;
use yii\widgets\Menu;

//UserAssets::register($this);

/* @var $content string */

 $this->beginContent(Module::getInstance()->mainLayout); ?>

    <?=
        Menu::widget([
            'items' => $this->params['menu'],
        ]);
    ?>

    <?= $content ?>

<?php $this->endContent();
