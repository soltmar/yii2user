<?php

/* @var $this \yii\web\View */
use mariusz_soltys\yii2user\assets\UserAssets;
use mariusz_soltys\yii2user\Module;
use yii\bootstrap\Nav;

//UserAssets::register($this);

/* @var $content string */

UserAssets::register($this);

$this->beginContent(Module::getInstance()->mainLayout); ?>

    <div class="row">
        <div class="col-md-12">
            <?=
            Nav::widget([
                'items' => Module::getInstance()->getMenu(),
                'options' => ['class' =>'nav-pills'], // set this to nav-tab to get tab-styled navigation
            ]);
            ?>
        </div>
        <div class="col-md-12">
            <?= $content ?>
        </div>
    </div>
<?php $this->endContent();