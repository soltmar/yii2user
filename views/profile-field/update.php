<?php

use mariusz_soltys\yii2user\Module;

/**
 * @var \yii\base\View $this
 * @var \mariusz_soltys\yii2user\models\ProfileField $model
 */

$this->params['breadcrumbs']= [
    ['label' => Module::t('Profile Fields'), 'url' => ['admin']],
    ['label' => $model->title, 'url' => ['view','id'=>$model->id]],
    Module::t('Update'),
];
Module::getInstance()->setMenu([
    ['label'=>Module::t('Create Profile Field'), 'url'=> ['/user/profile-field/create']],
    ['label'=>Module::t('View Profile Field'), 'url'=> ['view','id'=>$model->id]],
    ['label'=>Module::t('Manage Profile Field'), 'url'=> ['admin']],
    ['label'=>Module::t('Manage Users'), 'url'=> ['/user/admin']],
]);
?>

    <h1><?= Module::t('Update Profile Field ').$model->id; ?></h1>
<?= $this->render('_form', ['model'=>$model]); ?>