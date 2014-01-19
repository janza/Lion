<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
		'id' => 'prod-users-form',
		'enableAjaxValidation' => true,
	    ));
    ?>
    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
	Personal information
    </div>

    <div class="row">
	<?php echo $form->labelEx($model, 'first_name'); ?>
	<?php echo $form->textField($model, 'first_name', array('size' => 20, 'maxlength' => 40)); ?>
	<?php echo $form->error($model, 'first_name'); ?>
    </div>

    <div class="row">
	<?php echo $form->labelEx($model, 'last_name'); ?>
	<?php echo $form->textField($model, 'last_name', array('size' => 20, 'maxlength' => 50)); ?>
	<?php echo $form->error($model, 'last_name'); ?>
    </div>

    <p style="color: red;">
	<b>
	    <?php
	    echo (isset($model->passwdMessage) ? $model->passwdMessage : "");
	    ?>
	</b>
    </p>
    <div class="row">
	<?php echo $form->labelEx($model, 'password'); ?>
	<?php echo $form->passwordField($model, 'password', array('size' => 20, 'maxlength' => 100)); ?>
	<?php echo $form->error($model, 'password'); ?>
        </div>
    
        <div class="row">
	<?php echo $form->labelEx($model, 'PasswordConfirm'); ?>
	<?php echo $form->passwordField($model, 'PasswordConfirm', array('size' => 20, 'maxlength' => 50)); ?>
	<?php echo $form->error($model, 'PasswordConfirm'); ?>
        </div>

        <p style="color: red;">
    	<b>
	    <?php
	    echo (isset($model->message) ? $model->message : "");
	    ?>
	</b>
    </p>

    <div class="row">
	<?php echo $form->labelEx($model, 'username'); ?>
	<?php echo $form->textField($model, 'username', array('size' => 20, 'maxlength' => 30)); ?>
	<?php echo $form->error($model, 'username'); ?>
        </div>

        <div class="row">
            	Contact information
        </div>

        <div class="row">
	<?php echo $form->labelEx($model, 'email'); ?>
	<?php echo $form->textField($model, 'email', array('size' => 20, 'maxlength' => 50)); ?>
	<?php echo $form->error($model, 'email'); ?>
        </div>

        <div class="row">
	<?php echo $form->labelEx($model, 'phone'); ?>
	<?php echo $form->textField($model, 'phone', array('size' => 20, 'maxlength' => 20)); ?>
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