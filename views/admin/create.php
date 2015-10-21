<?php
$this->breadcrumbs=array(
	Module::t('Users')=>array('admin'),
	Module::t('Create'),
);

$this->menu=array(
    array('label'=>Module::t('Manage Users'), 'url'=>array('admin')),
    array('label'=>Module::t('Manage Profile Field'), 'url'=>array('profileField/admin')),
    array('label'=>Module::t('List User'), 'url'=>array('/user')),
);
?>
<h1><?php echo Module::t("Create User"); ?></h1>

<?php
	echo $this->renderPartial('_form', array('model'=>$model,'profile'=>$profile));
?>