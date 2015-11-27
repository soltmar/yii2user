<?php
use marsoltys\yii2user\Module;

/**
 * @var $this yii\web\View
 * @var \marsoltys\yii2user\models\User $model
 * @var \marsoltys\yii2user\models\Profile $profile
 */

$this->params['breadcrumbs']= [
    ['label' => Module::t('Users'), 'url' => ['admin']],
    ['label' => $model->username, 'url' => ['view','id'=>$model->id]],
    Module::t('Update'),
];
Module::getInstance()->addMenu(['label'=>Module::t('View User'), 'url'=> ['view','id'=>$model->id]]);
?>

    <h1><?php echo  Module::t('Update User')." ".$model->id; ?></h1>

<?=
 $this->render('_form', ['model' =>$model, 'profile' =>$profile]);
?>