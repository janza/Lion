<?php
$this->pageTitle = Yii::app()->name . ' - Login';
$this->breadcrumbs = array(
    'Login',
);
?>
<div class="post">
    <h2 class="title">Login</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<div class="roundedBox" id="type1">
	    <div class="form">
		<?php
		$form = $this->beginWidget('CActiveForm', array(
			    'id' => 'login-form',
			    'enableClientValidation' => true,
			    'clientOptions' => array(
				'validateOnSubmit' => true,
			    ),
			));
		?>
		<p>Please fill out the following form with your login credentials:</p>
		<p class="note">Fields with <span class="required">*</span> are required.</p>

		<div class="row">
		    <?php echo $form->labelEx($model, 'login_username'); ?>
		    <?php echo $form->textField($model, 'login_username'); ?>
		    <?php echo $form->error($model, 'login_username'); ?>
		</div>

		<div class="row">
		    <?php echo $form->labelEx($model, 'login_password'); ?>
		    <?php echo $form->passwordField($model, 'login_password'); ?>
		    <?php echo $form->error($model, 'login_password'); ?>
		</div>

		<div class="row login_remember">
		    <?php echo $form->checkBox($model, 'login_remember'); ?>
		    <?php echo $form->label($model, 'login_remember'); ?>
		    <?php echo $form->error($model, 'login_remember'); ?>
		</div>

		<div class="row buttons">
		    <?php echo CHtml::submitButton('Login'); ?>
		</div>
		<div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div>
	    </div>
	</div>

	<?php $this->endWidget(); ?>
	    	<p id="register_here">Not a member? <a href="<?php echo Yii::app()->request->baseUrl . "/index.php/site/registrationForm"; ?>">Register here!</a></p>
    </div><!-- form -->
</div>
