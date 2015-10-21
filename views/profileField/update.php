<?php
$this->breadcrumbs=array(
	Module::t('Profile Fields')=>array('admin'),
	$model->title=>array('view','id'=>$model->id),
	Module::t('Update'),
);
$this->menu=array(
    array('label'=>Module::t('Create Profile Field'), 'url'=>array('create')),
    array('label'=>Module::t('View Profile Field'), 'url'=>array('view','id'=>$model->id)),
    array('label'=>Module::t('Manage Profile Field'), 'url'=>array('admin')),
    array('label'=>Module::t('Manage Users'), 'url'=>array('/user/admin')),
);
?>

<h1><?php echo Module::t('Update Profile Field ').$model->id; ?></h1>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>