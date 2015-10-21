<?php
$this->breadcrumbs=array(
	Module::t('Profile Fields')=>array('admin'),
	Module::t('Create'),
);
$this->menu=array(
    array('label'=>Module::t('Manage Profile Field'), 'url'=>array('admin')),
    array('label'=>Module::t('Manage Users'), 'url'=>array('/user/admin')),
);
?>
<h1><?php echo Module::t('Create Profile Field'); ?></h1>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>