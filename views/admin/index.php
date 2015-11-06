<?php
use mariusz_soltys\yii2user\components\UHtml;
use mariusz_soltys\yii2user\models\User;
use mariusz_soltys\yii2user\Module;
use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \mariusz_soltys\yii2user\models\search\UserSearch $searchModel
 * @var \yii\data\ActiveDataProvider $dataProvider
 */

$this->params['breadcrumbs']= [
    ['label' => Module::t('Users'), 'url' => ['/user']],
    Module::t('Manage'),
];

$this->registerJs("
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});	
$('.search-form form').submit(function(){
    $.fn.yiiGridView.update('user-grid', {
        data: $(this).serialize()
    });
    return false;
});
");

?>
<h1><?= Module::t("Manage Users"); ?></h1>

<p><?= Module::t("You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done."); ?></p>

<?= Html::a(Module::t('Advanced Search'), '#', ['class'=>'search-button']); ?>

<div class="search-form" style="display:none">
    <?= $this->render('_search', ['model' => $searchModel]); ?>
</div><!-- search-form -->

<?php \yii\widgets\Pjax::begin(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'id',
            'format'=>'raw',
            'value' => function ($data) {
                return Html::a(Html::encode($data->id), ["admin/update", "id" => $data->id]);
            },
        ],
        [
            'attribute' => 'username',
            'format'=>'raw',
            'value' => function ($data) {
                return Html::a(UHtml::markSearch($data, "username"), ["admin/view", "id"=>$data->id]);
            },
        ],
        [
            'attribute'=>'email',
            'format'=>'raw',
            'value'=> function ($data) {
                return Html::a(UHtml::markSearch($data, "email"), "mailto:".$data->email);
            },
        ],
        'create_at',
        'lastvisit_at',
        [
            'attribute'=>'superuser',
            'value' => function ($data) {
                return User::itemAlias("AdminStatus", $data->superuser);
            },
            'filter'=>User::itemAlias("AdminStatus"),
        ],
        [
            'attribute'=>'status',
            'value'=> function ($data) {
                return User::itemAlias("UserStatus", $data->status);
            },
            'filter' => User::itemAlias("UserStatus"),
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => Module::t('Actions')
        ],
    ]
]); ?>

<?php \yii\widgets\Pjax::end(); ?>

<?php //$this->widget('zii.widgets.grid.CGridView', array(
//    'id'=>'user-grid',
//    'dataProvider'=>$model->search(),
//    'filter'=>$model,
//    'columns'=>array(
//        array(
//            'name' => 'id',
//            'type'=>'raw',
//            'value' => 'CHtml::link(CHtml::encode($data->id),array("admin/update","id"=>$data->id))',
//        ),
//        array(
//            'name' => 'username',
//            'type'=>'raw',
//            'value' => 'CHtml::link(UHtml::markSearch($data,"username"),array("admin/view","id"=>$data->id))',
//        ),
//        array(
//            'name'=>'email',
//            'type'=>'raw',
//            'value'=>'CHtml::link(UHtml::markSearch($data,"email"), "mailto:".$data->email)',
//        ),
//        'create_at',
//        'lastvisit_at',
//        array(
//            'name'=>'superuser',
//            'value'=>'User::itemAlias("AdminStatus",$data->superuser)',
//            'filter'=>User::itemAlias("AdminStatus"),
//        ),
//        array(
//            'name'=>'status',
//            'value'=>'User::itemAlias("UserStatus",$data->status)',
//            'filter' => User::itemAlias("UserStatus"),
//        ),
//        array(
//            'class'=>'CButtonColumn',
//        ),
//    ),
//)); ?>
