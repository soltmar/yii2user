<?php
    use marsoltys\yii2user\models\Profile;
    use marsoltys\yii2user\models\ProfileField;
    use marsoltys\yii2user\models\User;
    use marsoltys\yii2user\Module;
    use yii\widgets\DetailView;

    /**
     * @var \yii\web\View $this
     * @var \marsoltys\yii2user\models\User $model
     */

    $this->params['breadcrumbs']= [
        ['label' => Module::t('Users'), 'url' => ['admin']],
        $model->username,
    ];

    Module::getInstance()->addMenu(['label'=>Module::t('Update User'), 'url'=> ['update','id'=>$model->id]], 3);
    Module::getInstance()->addMenu([
        'label'=>Module::t('Delete User'),
        'url'=>'#',
        'linkOptions'=> [
            'submit'=> ['delete','id'=>$model->id],
            'confirm'=>Module::t('Are you sure to delete this item?')
        ]
    ], 4);

    $this->title = Module::t('View User').' "'.$model->username.'"';
?>

<h1><?= Module::t('View User').' "'.$model->username.'"'; ?></h1>

<?php

    $attributes = [
        'id',
        'username',
    ];

    $profileFields = ProfileField::find()->forOwner()->sort()->all();
    if ($profileFields) {
        foreach ($profileFields as $field) {
            $val = '';
            if ($field->widgetView($model->profile)) {
                $val = $field->widgetView($model->profile);
            } else {
                if ($field->range) {
                    $val = Profile::range($field->range, $model->profile->getAttribute($field->varname));
                } else {
                    $val = $model->profile->getAttribute($field->varname);
                }
            }

            $type = 'html';

            if ($field->field_type == "DATE" || $field->widget=="UWjuidate") {
                $type = 'date';
            }
            array_push($attributes, [
                'label' => Module::t($field->title),
                'name' => $field->varname,
                'format'=> $type,
                'value' => $val
            ]);
        }
    }

    array_push(
        $attributes,
        'password',
        'email:email',
        'activkey',
        'create_at:date',
        'lastvisit_at:date',
        [
            'attribute' => 'superuser',
            'value' => User::itemAlias("AdminStatus", $model->superuser),
        ],
        [
            'attribute' => 'status',
            'value' => User::itemAlias("UserStatus", $model->status),
        ]
    );

    echo DetailView::widget([
        'model' => $model,
        'attributes'=>$attributes,
    ]);


?>
