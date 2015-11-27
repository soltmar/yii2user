<?php
use marsoltys\yii2user\models\ProfileField;
use marsoltys\yii2user\Module;
use yii\widgets\DetailView;

$this->params['breadcrumbs']= [
    ['label' => Module::t('Users'), 'url' => ['index']],
    $model->username,
];
Module::getInstance()->setMenu([
    ['label'=>Module::t('List User'), 'url'=> ['index']]
]);
?>
<h1><?php echo Module::t('View User').' "'.$model->username.'"'; ?></h1>
<?php

    // For all users
    $attributes = [
        'username',
    ];

    $profileFields=ProfileField::find()->forAll()->sort()->all();
    if ($profileFields) {
        foreach ($profileFields as $field) {
            array_push($attributes, [
                'label' => Module::t($field->title),
                'attribute' => $field->varname,
                'value' => (($field->widgetView($model->profile))?$field->widgetView($model->profile):(($field->range)?Profile::range($field->range,$model->profile->getAttribute($field->varname)):$model->profile->getAttribute($field->varname))),

            ]);
        }
    }
    array_push(
        $attributes,
        'create_at',
        [
            'attribute' => 'lastvisit_at',
            'value' => (($model->lastvisit_at!='0000-00-00 00:00:00')?$model->lastvisit_at:Module::t('Not visited')),
        ]
    );

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
    ]);

?>
