<?php

use marsoltys\yii2user\Module;
use yii\bootstrap\Nav;

Nav::widget([
    'items' => [
        ['label'=>Module::t('Create User'), 'url'=> ['create']],
        ['label'=>Module::t('Manage Users'), 'url'=> ['admin']],
        ['label'=>Module::t('Manage Profile Field'), 'url'=> ['profileField/admin']],
        ['label'=>Module::t('List User'), 'url'=> ['/user']],
    ],
    'options' => ['class' =>'nav-pills'], // set this to nav-tab to get tab-styled navigation
]);
