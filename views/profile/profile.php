<?php

use mariusz_soltys\yii2user\models\Profile;
use mariusz_soltys\yii2user\models\ProfileField;
use mariusz_soltys\yii2user\models\User;
use mariusz_soltys\yii2user\Module;
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var mariusz_soltys\yii2user\models\User $model
 * @var mariusz_soltys\yii2user\models\Profile $profile
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

<?php if (Yii::$app->user->hasFlash('profileMessage')) : ?>
    <div class="success">
        <?php echo Yii::$app->user->getFlash('profileMessage'); ?>
    </div>
<?php endif; ?>
<table class="dataGrid">
    <tr>
        <th><?= Html::encode($model->getAttributeLabel('username')); ?></th>
        <td><?= Html::encode($model->username); ?></td>
    </tr>
    <?php
    $profileFields = ProfileField::find()->forOwner()->sort()->all();
    if ($profileFields) {
        foreach ($profileFields as $field) {
            /** $field */
            ?>
            <tr>
                <th ><?= Html::encode(Module::t($field->title)); ?></th>
                <td>
                    <?php
                    if ($field->widgetView($profile)) {
                        $html = $field->widgetView($profile);
                        echo $html;
                    } else {
                        if (Html::encode(($field->range))) {
                            echo Profile::range($field->range, $profile->getAttribute($field->varname));
                        } else {
                            echo $profile->getAttribute($field->varname);
                        }
                    }
                    ?>
                </td>
            </tr>
            <?php
        }//$profile->getAttribute($field->varname)
    }
    ?>
    <tr>
        <th><?= Html::encode($model->getAttributeLabel('email')); ?></th>
        <td><?= Html::encode($model->email); ?></td>
    </tr>
    <tr>
        <th><?= Html::encode($model->getAttributeLabel('create_at')); ?></th>
        <td><?= $model->create_at; ?></td>
    </tr>
    <tr>
        <th><?= Html::encode($model->getAttributeLabel('lastvisit_at')); ?></th>
        <td><?= $model->lastvisit_at; ?></td>
    </tr>
    <tr>
        <th><?= Html::encode($model->getAttributeLabel('status')); ?></th>
        <td><?= Html::encode(User::itemAlias("UserStatus", $model->status)); ?></td>
    </tr>
</table>
