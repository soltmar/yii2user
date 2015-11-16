<?php
use mariusz_soltys\yii2user\Module;

$this->params['breadcrumbs']= [
    ['label' => Module::t('Profile Fields'), 'url' => ['admin']],
    Module::t('Create'),
];
?>
    <h1><?php echo Module::t('Create Profile Field'); ?></h1>
<?= $this->render('_form', ['model' =>$model]); ?>