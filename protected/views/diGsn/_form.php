<div class="roundedBox" id="type1">
    <div class="form">

	<?php
	$form = $this->beginWidget('CActiveForm', array(
		    'id' => 'di-gsn-form',
		    'enableAjaxValidation' => false,
		));
	?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
	    <?php echo $form->labelEx($model, 'gsn_name'); ?>
	    <?php echo $form->textField($model, 'gsn_name', array('size' => 40, 'maxlength' => 40)); ?>
	    <?php echo $form->error($model, 'gsn_name'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'gsn_url'); ?>
	    <?php echo $form->textField($model, 'gsn_url', array('size' => 40, 'maxlength' => 40)); ?>
	    <?php echo $form->error($model, 'gsn_url'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'city'); ?>
	    <?php echo $form->textField($model, 'city', array('size' => 30, 'maxlength' => 30)); ?>
	    <?php echo $form->error($model, 'city'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'state'); ?>
	    <?php echo $form->textField($model, 'state', array('size' => 30, 'maxlength' => 30)); ?>
	    <?php echo $form->error($model, 'state'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'is_active'); ?>
	    <?php echo $form->textField($model, 'is_active', array('size' => 1, 'maxlength' => 1)); ?>
	    <?php echo $form->error($model, 'is_active'); ?>
	</div>


	<?php /*
	      <div class="row">
	      <?php echo $form->labelEx($model,'last_change'); ?>
	      <?php echo $form->textField($model,'last_change'); ?>
	      <?php echo $form->error($model,'last_change'); ?>
	      </div>

	      <div class="row">
	      <?php echo $form->labelEx($model,'is_dummy'); ?>
	      <?php echo $form->textField($model,'is_dummy',array('size'=>1,'maxlength'=>1)); ?>
	      <?php echo $form->error($model,'is_dummy'); ?>
	      </div>

	      <div class="row">
	      <?php echo $form->labelEx($model,'date_activated_id'); ?>
	      <?php echo $form->textField($model,'date_activated_id'); ?>
	      <?php echo $form->error($model,'date_activated_id'); ?>
	      </div>

	      <div class="row">
	      <?php echo $form->labelEx($model,'date_deactivated_id'); ?>
	      <?php echo $form->textField($model,'date_deactivated_id'); ?>
	      <?php echo $form->error($model,'date_deactivated_id'); ?>
	      </div>

	     */ ?>

    	<div class="row">
	    <?php echo $form->labelEx($model, 'username'); ?>
	    <?php echo $form->textField($model, 'username', array('size' => 30, 'maxlength' => 30)); ?>
	    <?php echo $form->error($model, 'username'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'password'); ?>
	    <?php echo $form->passwordField($model, 'password', array('size' => 30, 'maxlength' => 30)); ?>
	    <?php echo $form->error($model, 'password'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'gsn_ip'); ?>
	    <?php echo $form->textField($model, 'gsn_ip', array('size' => 16, 'maxlength' => 16)); ?>
	    <?php echo $form->error($model, 'gsn_ip'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'gsn_port'); ?>
	    <?php echo $form->textField($model, 'gsn_port'); ?>
	    <?php echo $form->error($model, 'gsn_port'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'port_ssl'); ?>
	    <?php echo $form->textField($model, 'port_ssl'); ?>
	    <?php echo $form->error($model, 'port_ssl'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'database_schema'); ?>
	    <?php echo $form->textField($model, 'database_schema', array('size' => 30, 'maxlength' => 30)); ?>
	    <?php echo $form->error($model, 'database_schema'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'database_user'); ?>
	    <?php echo $form->textField($model, 'database_user', array('size' => 30, 'maxlength' => 30)); ?>
	    <?php echo $form->error($model, 'database_user'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'database_password'); ?>
	    <?php echo $form->textField($model, 'database_password', array('size' => 30, 'maxlength' => 30)); ?>
	    <?php echo $form->error($model, 'database_password'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'database_port'); ?>
	    <?php echo $form->textField($model, 'database_port'); ?>
	    <?php echo $form->error($model, 'database_port'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'sftp_username'); ?>
	    <?php echo $form->textField($model, 'sftp_username', array('size' => 30, 'maxlength' => 100)); ?>
	    <?php echo $form->error($model, 'sftp_username'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'sftp_password'); ?>
	    <?php echo $form->textField($model, 'sftp_password', array('size' => 30, 'maxlength' => 100)); ?>
	    <?php echo $form->error($model, 'sftp_password'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'notification_folder'); ?>
	    <?php echo $form->textField($model, 'notification_folder', array('size' => 30, 'maxlength' => 150)); ?>
	    <?php echo $form->error($model, 'notification_folder'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'notification_backup_folder'); ?>
	    <?php echo $form->textField($model, 'notification_backup_folder', array('size' => 30, 'maxlength' => 150)); ?>
	    <?php echo $form->error($model, 'notification_backup_folder'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'gsn_home_folder'); ?>
	    <?php echo $form->textField($model, 'gsn_home_folder', array('size' => 30, 'maxlength' => 150)); ?>
	    <?php echo $form->error($model, 'gsn_home_folder'); ?>
	</div>

	<div class="row buttons">
	    <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

	<?php $this->endWidget(); ?>

    </div><!-- form -->

    <div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div>
</div>