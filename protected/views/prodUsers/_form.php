<div class="roundedBox" id="type1">
    <div class="form">

	<?php
	$form = $this->beginWidget('CActiveForm', array(
		    'id' => 'prod-users-form',
		    'enableAjaxValidation' => false,
		));
	?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
	    <?php echo $form->labelEx($model, 'first_name'); ?>
	    <?php echo $form->textField($model, 'first_name', array('size' => 40, 'maxlength' => 40)); ?>
	    <?php echo $form->error($model, 'first_name'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'last_name'); ?>
	    <?php echo $form->textField($model, 'last_name', array('size' => 40, 'maxlength' => 50)); ?>
	    <?php echo $form->error($model, 'last_name'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'email'); ?>
	    <?php echo $form->textField($model, 'email', array('size' => 40, 'maxlength' => 50)); ?>
	    <?php echo $form->error($model, 'email'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'password'); ?>
	    <?php echo $form->passwordField($model, 'password', array('size' => 40, 'maxlength' => 100)); ?>
	    <?php echo $form->error($model, 'password'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'group_id'); ?>
	    <?php echo $form->textField($model, 'group_id'); ?>
	    <?php echo $form->error($model, 'group_id'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'active'); ?>
	    <?php echo $form->textField($model, 'active'); ?>
	    <?php echo $form->error($model, 'active'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'username'); ?>
	    <?php echo $form->textField($model, 'username', array('size' => 40, 'maxlength' => 30)); ?>
	    <?php echo $form->error($model, 'username'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'phone'); ?>
	    <?php echo $form->textField($model, 'phone', array('size' => 40, 'maxlength' => 20)); ?>
	    <?php echo $form->error($model, 'phone'); ?>
	</div>

        <div class="row">
	    <?php echo $form->labelEx($model, 'verify_code'); ?>
	    <div>
		<?php $this->widget('CCaptcha'); ?>
		<?php echo $form->textField($model, 'verify_code'); ?>
	    </div>
	    <div class="hint">Please enter the letters as they are shown in the image above.
		<br/>Letters are not case-sensitive.</div>
	    <?php echo $form->error($model, 'verify_code'); ?>
            </div>

    	<div class="row buttons">
	    <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
    	</div>

	<?php $this->endWidget(); ?>

    </div><!-- form -->
    <div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div>
</div>