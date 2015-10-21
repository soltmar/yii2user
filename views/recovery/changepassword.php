<?php $this->pageTitle=Yii::$app->name . ' - '.Module::t("Change password");
$this->breadcrumbs=array(
	Module::t("Login") => array('/user/login'),
	Module::t("Change password"),
);
?>

<h1><?php echo Module::t("Change password"); ?></h1>


<div class="form">
<?php echo CHtml::beginForm(); ?>

	<p class="note"><?php echo Module::t('Fields with <span class="required">*</span> are required.'); ?></p>
	<?php echo CHtml::errorSummary($form); ?>
	
	<div class="row">
	<?php echo CHtml::activeLabelEx($form,'password'); ?>
	<?php echo CHtml::activePasswordField($form,'password'); ?>
	<p class="hint">
	<?php echo Module::t("Minimal password length 4 symbols."); ?>
	</p>
	</div>
	
	<div class="row">
	<?php echo CHtml::activeLabelEx($form,'verifyPassword'); ?>
	<?php echo CHtml::activePasswordField($form,'verifyPassword'); ?>
	</div>
	
	
	<div class="row submit">
	<?php echo CHtml::submitButton(Module::t("Save")); ?>
	</div>

<?php echo CHtml::endForm(); ?>
</div><!-- form -->