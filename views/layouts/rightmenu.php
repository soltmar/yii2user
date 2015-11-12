<?php

/* @var $this \yii\web\View */
use mariusz_soltys\yii2user\assets\UserAssets;
use mariusz_soltys\yii2user\Module;
use yii\bootstrap\Nav;

//UserAssets::register($this);

/* @var $content string */

UserAssets::register($this);

$this->beginContent('@mariusz_soltys/yii2user/views/layouts/main.php'); ?>

    <div class="row">
        <div class="col-md-10">
            <?= $content ?>
        </div>
        <div class="col-md-2">
            <?=
            Nav::widget([
                'items' => Module::getInstance()->getMenu(),
                'options' => ['class' =>'nav-pills nav-stacked'], // set this to nav-tab to get tab-styled navigation
            ]);
            ?>
        </div>
    </div>
<?php $this->endContent();