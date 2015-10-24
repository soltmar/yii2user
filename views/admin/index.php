<?php
use mariusz_soltys\yii2user\Module;
use yii\helpers\Html;

$this->params['breadcrumbs']=array(
    Module::t('Users')=>array('/user'),
    Module::t('Manage'),
);

$this->params['menu']= [
    ['label'=>Module::t('Create User'), 'url'=> ['create']],
    ['label'=>Module::t('Manage Users'), 'url'=> ['admin']],
    ['label'=>Module::t('Manage Profile Field'), 'url'=> ['profileField/admin']],
    ['label'=>Module::t('List User'), 'url'=> ['/user']],
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

<?= Html::a(Module::t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
    <?php $this->render('_search', [
        'model'=>$model,
    ]); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'user-grid',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        array(
            'name' => 'id',
            'type'=>'raw',
            'value' => 'CHtml::link(CHtml::encode($data->id),array("admin/update","id"=>$data->id))',
        ),
        array(
            'name' => 'username',
            'type'=>'raw',
            'value' => 'CHtml::link(UHtml::markSearch($data,"username"),array("admin/view","id"=>$data->id))',
        ),
        array(
            'name'=>'email',
            'type'=>'raw',
            'value'=>'CHtml::link(UHtml::markSearch($data,"email"), "mailto:".$data->email)',
        ),
        'create_at',
        'lastvisit_at',
        array(
            'name'=>'superuser',
            'value'=>'User::itemAlias("AdminStatus",$data->superuser)',
            'filter'=>User::itemAlias("AdminStatus"),
        ),
        array(
            'name'=>'status',
            'value'=>'User::itemAlias("UserStatus",$data->status)',
            'filter' => User::itemAlias("UserStatus"),
        ),
        array(
            'class'=>'CButtonColumn',
        ),
    ),
)); ?>
