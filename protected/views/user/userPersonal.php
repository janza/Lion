<?php
$this->pageTitle = Yii::app()->name . ' - Personal';
$this->breadcrumbs = array(
    'Personal',
);
?>

<div class="post">

    <h2 class="title">User personal information</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<?php
	    if (isset($message)){
	    echo $message;
	    }
	?>
	<div class="roundedBox" id="type1">
	    <h3>Welcome
	    <?php
	    echo "&nbsp" . $model->first_name . " " . $model->last_name;
	    echo "<br/>";
?>
	    </h3>

	        <div class="form">

  <?php
	$form = $this->beginWidget('CActiveForm', array(
		    'id' => 'user-personal-form',
		    'enableAjaxValidation' => false,
		));
	?>
	    	<?php echo $form->errorSummary($model); ?>

	    If you are interested in changing your username, feel free to do so.
	    	<div class="row">
	    <?php echo $form->labelEx($model, 'username'); ?>
	    <?php echo $form->textField($model, 'username', array('size' => 30, 'maxlength' => 30)); ?>
	    <?php echo $form->error($model, 'username'); ?>
	</div>
	    To change your password simply provide a new one below.
	    <div class="row">
		<?php echo $form->labelEx($model, 'password'); ?>
		<?php echo $form->passwordField($model, 'password', array('size' => 30, 'maxlength' => 100)); ?>
		<?php echo $form->error($model, 'password'); ?>
	    </div>

	    <div class="row">
	    <?php echo $form->labelEx($model, 'first_name'); ?>
	    <?php echo $form->textField($model, 'first_name', array('size' => 30, 'maxlength' => 40)); ?>
	    <?php echo $form->error($model, 'first_name'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'last_name'); ?>
	    <?php echo $form->textField($model, 'last_name', array('size' => 30, 'maxlength' => 50)); ?>
	    <?php echo $form->error($model, 'last_name'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'email'); ?>
	    <?php echo $form->textField($model, 'email', array('size' => 30, 'maxlength' => 50)); ?>
	    <?php echo $form->error($model, 'email'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model, 'phone'); ?>
	    <?php echo $form->textField($model, 'phone', array('size' => 30, 'maxlength' => 20)); ?>
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
	    </div>
	    <div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div>
	</div>

    </div>
</div>
