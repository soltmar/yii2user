<?php

use marsoltys\yii2user\models\Profile;
use marsoltys\yii2user\models\ProfileField;
use marsoltys\yii2user\models\User;
use marsoltys\yii2user\Module;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var $this yii\web\View
 * @var marsoltys\yii2user\models\User $model
 * @var marsoltys\yii2user\models\Profile $profile
 */

$this->title=Yii::$app->name . ' - '.Module::t("Profile");
$this->params['breadcrumbs'][] = Module::t("Profile");

$menu = [
    ['label'=>Module::t('List User'), 'url'=> ['/user/user/index']],
    ['label'=>Module::t('Edit'), 'url'=> ['edit']],
    ['label'=>Module::t('Change password'), 'url'=> ['changepassword']],
    ['label'=>Module::t('Logout'), 'url'=> ['/user/logout']],
];

if (Module::isAdmin()) {
    array_unshift($menu, ['label'=>Module::t('Manage Users'), 'url'=> ['/user/admin']]);
}

Module::getInstance()->setMenu($menu);

?>
    <h1><?= Module::t('Your profile'); ?></h1>

<?php

$attributes = [
    'username',
    'email:email',
    'create_at:date',
    'lastvisit_at:date'
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


echo DetailView::widget([
    'model' => $model,
    'attributes'=>$attributes,
]);