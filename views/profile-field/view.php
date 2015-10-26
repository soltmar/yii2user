<?php
use mariusz_soltys\yii2user\Module;
use yii\widgets\DetailView;

$this->params['breadcrumbs']= [
    ['label' => Module::t('Profile Fields'), 'url' => ['admin']],
    Module::t($model->title),
];

Module::getInstance()->setMenu([
    ['label'=>Module::t('Create Profile Field'), 'url'=> ['create']],
    ['label'=>Module::t('Update Profile Field'), 'url'=> ['update','id'=>$model->id]],
    ['label'=>Module::t('Delete Profile Field'), 'url'=>'#',
        'linkOptions'=> ['submit'=> ['delete','id'=>$model->id],'confirm'=>Module::t('Are you sure to delete this item?')]],
    ['label'=>Module::t('Manage Profile Field'), 'url'=> ['admin']],
    ['label'=>Module::t('Manage Users'), 'url'=> ['/user/admin']],
]);
?>
<h1><?php echo Module::t('View Profile Field #').$model->varname; ?></h1>

<?= DetailView::widget([
    'model' => $model,
    'attributes'=> [
        'id',
        'varname',
        'title',
        'field_type',
        'field_size',
        'field_size_min',
        'required',
        'match',
        'range',
        'error_message',
        'other_validator',
        'widget',
        'widgetparams',
        'default',
        'position',
        'visible',
    ]
]); ?>
