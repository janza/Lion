<?php
$this->pageTitle = Yii::app()->name . ' - Contact Us';
$this->breadcrumbs = array(
    'Contact',
);
?>

<div class="post">

    <h2 class="title">Contact us</h2>
    <p class="posted">Lion development team</p>

    <div class="entry">
	<div class="roundedBox" id="type1">
	    <p>
		<?php
		echo (isset($model->message) ? $model->message : "If you have business inquiries or other questions, please fill out the following form to contact us. Thank you in advance.");
		?>
	    </p>

	    <div class="form">

		<?php
		$form = $this->beginWidget('CActiveForm', array(
			    'id' => 'contact-form',
			    'enableClientValidation' => true,
			    'clientOptions' => array(
				'validateOnSubmit' => true,
			    ),
			));
		?>

		<p class="note">Fields with <span class="required">*</span> are required.</p>

		<?php echo $form->errorSummary($model); ?>

		<div class="row">
		    <?php echo $form->labelEx($model, 'name'); ?>
		    <?php echo $form->textField($model, 'name'); ?>
		    <?php echo $form->error($model, 'name'); ?>
		</div>

		<div class="row">
		    <?php echo $form->labelEx($model, 'email'); ?>
		    <?php echo $form->textField($model, 'email'); ?>
		    <?php echo $form->error($model, 'email'); ?>
		</div>

		<div class="row">
		    <?php echo $form->labelEx($model, 'subject'); ?>
		    <?php echo $form->textField($model, 'subject', array('size' => 60, 'maxlength' => 128)); ?>
		    <?php echo $form->error($model, 'subject'); ?>
		</div>

		<div class="row">
		    <?php echo $form->labelEx($model, 'body'); ?>
		    <?php echo $form->textArea($model, 'body', array('rows' => 6, 'cols' => 50)); ?>
		    <?php echo $form->error($model, 'body'); ?>
		</div>

		<?php if (CCaptcha::checkRequirements()): ?>
			<div class="row">
		    <?php echo $form->labelEx($model, 'verifyCode'); ?>
    		    <div>
			<?php $this->widget('CCaptcha'); ?>
			<?php echo $form->textField($model, 'verifyCode'); ?>
		    </div>
		    <div class="hint">Please enter the letters as they are shown in the image above.
			<br/>Letters are not case-sensitive.</div>
		    <?php echo $form->error($model, 'verifyCode'); ?>
    		</div>
		<?php endif; ?>

			<div class="row buttons">
		    <?php echo CHtml::submitButton('Submit'); ?>
    		</div>

		<?php $this->endWidget(); ?>

	    </div><!-- form -->
	    <div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div>
	</div>

    </div>
</div>
