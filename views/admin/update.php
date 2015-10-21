<?php
$this->breadcrumbs=array(
	(Module::t('Users'))=>array('admin'),
	$model->username=>array('view','id'=>$model->id),
	(Module::t('Update')),
);
$this->menu=array(
    array('label'=>Module::t('Create User'), 'url'=>array('create')),
    array('label'=>Module::t('View User'), 'url'=>array('view','id'=>$model->id)),
    array('label'=>Module::t('Manage Users'), 'url'=>array('admin')),
    array('label'=>Module::t('Manage Profile Field'), 'url'=>array('profileField/admin')),
    array('label'=>Module::t('List User'), 'url'=>array('/user')),
);
?>

<h1><?php echo  Module::t('Update User')." ".$model->id; ?></h1>

<?php
	echo $this->renderPartial('_form', array('model'=>$model,'profile'=>$profile));
?>