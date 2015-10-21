<?php
$this->breadcrumbs=array(
	Module::t("Users"),
);
if(Module::isAdmin()) {
	$this->layout='//layouts/column2';
	$this->menu=array(
	    array('label'=>Module::t('Manage Users'), 'url'=>array('/user/admin')),
	    array('label'=>Module::t('Manage Profile Field'), 'url'=>array('profileField/admin')),
	);
}
?>

<h1><?php echo Module::t("List User"); ?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
			'name' => 'username',
			'type'=>'raw',
			'value' => 'CHtml::link(CHtml::encode($data->username),array("user/view","id"=>$data->id))',
		),
		'create_at',
		'lastvisit_at',
	),
)); ?>
