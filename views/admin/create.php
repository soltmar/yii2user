<?php
use mariusz_soltys\yii2user\Module;

/**
 * @var \yii\web\View $this
 * @var \mariusz_soltys\yii2user\models\User $model
 * @var \mariusz_soltys\yii2user\models\Profile $profile
 */

$this->params['breadcrumbs']= [
    ['label' => Module::t('Users'), 'url' => ['admin']],
    Module::t('Create'),
];

?>
    <h1><?php echo Module::t("Create User"); ?></h1>

<?= $this->render('_form', ['model'=>$model, 'profile'=>$profile]); ?>