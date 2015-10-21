<?php
$this->breadcrumbs=array(
	Module::t('Profile Fields')=>array('admin'),
	Module::t($model->title),
);
$this->menu=array(
    array('label'=>Module::t('Create Profile Field'), 'url'=>array('create')),
    array('label'=>Module::t('Update Profile Field'), 'url'=>array('update','id'=>$model->id)),
    array('label'=>Module::t('Delete Profile Field'), 'url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>Module::t('Are you sure to delete this item?'))),
    array('label'=>Module::t('Manage Profile Field'), 'url'=>array('admin')),
    array('label'=>Module::t('Manage Users'), 'url'=>array('/user/admin')),
);
?>
<h1><?php echo Module::t('View Profile Field #').$model->varname; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
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
	),
)); ?>
