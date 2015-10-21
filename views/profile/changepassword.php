<?php $this->pageTitle=Yii::$app->name . ' - '.Module::t("Change password");
$this->breadcrumbs=array(
	Module::t("Profile") => array('/user/profile'),
	Module::t("Change password"),
);
$this->menu=array(
	((Module::isAdmin())
		?array('label'=>Module::t('Manage Users'), 'url'=>array('/user/admin'))
		:array()),
    array('label'=>Module::t('List User'), 'url'=>array('/user')),
    array('label'=>Module::t('Profile'), 'url'=>array('/user/profile')),
    array('label'=>Module::t('Edit'), 'url'=>array('edit')),
    array('label'=>Module::t('Logout'), 'url'=>array('/user/logout')),
);
?>

<h1><?php echo Module::t("Change password"); ?></h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'changepassword-form',
	'enableAjaxValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<p class="note"><?php echo Module::t('Fields with <span class="required">*</span> are required.'); ?></p>
	<?php echo $form->errorSummary($model); ?>
	
	<div class="row">
	<?php echo $form->labelEx($model,'oldPassword'); ?>
	<?php echo $form->passwordField($model,'oldPassword'); ?>
	<?php echo $form->error($model,'oldPassword'); ?>
	</div>
	
	<div class="row">
	<?php echo $form->labelEx($model,'password'); ?>
	<?php echo $form->passwordField($model,'password'); ?>
	<?php echo $form->error($model,'password'); ?>
	<p class="hint">
	<?php echo Module::t("Minimal password length 4 symbols."); ?>
	</p>
	</div>
	
	<div class="row">
	<?php echo $form->labelEx($model,'verifyPassword'); ?>
	<?php echo $form->passwordField($model,'verifyPassword'); ?>
	<?php echo $form->error($model,'verifyPassword'); ?>
	</div>
	
	
	<div class="row submit">
	<?php echo CHtml::submitButton(Module::t("Save")); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->