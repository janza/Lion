<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'agg-day-hourly-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'gsn_id'); ?>
		<?php echo $form->textField($model,'gsn_id'); ?>
		<?php echo $form->error($model,'gsn_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sensor_id'); ?>
		<?php echo $form->textField($model,'sensor_id'); ?>
		<?php echo $form->error($model,'sensor_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'unit_id'); ?>
		<?php echo $form->textField($model,'unit_id'); ?>
		<?php echo $form->error($model,'unit_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'date_id'); ?>
		<?php echo $form->textField($model,'date_id'); ?>
		<?php echo $form->error($model,'date_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'hour'); ?>
		<?php echo $form->textField($model,'hour'); ?>
		<?php echo $form->error($model,'hour'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'avg_value'); ?>
		<?php echo $form->textField($model,'avg_value'); ?>
		<?php echo $form->error($model,'avg_value'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'max_value'); ?>
		<?php echo $form->textField($model,'max_value'); ?>
		<?php echo $form->error($model,'max_value'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'min_value'); ?>
		<?php echo $form->textField($model,'min_value'); ?>
		<?php echo $form->error($model,'min_value'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'amplitude'); ?>
		<?php echo $form->textField($model,'amplitude'); ?>
		<?php echo $form->error($model,'amplitude'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->