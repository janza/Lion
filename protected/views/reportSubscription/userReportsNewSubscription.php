<?php
$this->pageTitle = Yii::app()->name . ' - New report subscription';
$this->breadcrumbs = array(
    'New report subscription',
);
?>
<div class="post">
    <p class="date"><?php echo date("M"); ?><b><?php echo date("j"); ?></b></p>
    <h2 class="title">New report subscription</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<div class="form">
	    <?php
	    $form = $this->beginWidget('CActiveForm', array(
			'id' => 'user_new_report_subscription-form',
			'enableAjaxValidation' => false,
		    ));
	    ?>
	    <p>
		<?php
		echo (isset($model->message) ? $model->message : "");
		?>
	    </p>
	    <p class="note">Fields with <span class="required">*</span> are required.</p>

	    <?php echo $form->errorSummary($model); ?>
    	    <h3>Type of the report</h3>
    	    <div class="row">
		<?php echo $form->labelEx($model, 'report_type'); ?>
		<div class="compactRadioGroup">
		    <?php
		    echo $form->radioButtonList($model, 'report_type',
			    array(1 => 'Daily',
				2 => 'Monthly',
			    ), array('separator' => "  ",
			'uncheckValue' => null,
			    // 'onChange'=>CHtml::ajax(array('type'=>'POST', 'url'=>CController::createUrl('dynamicRecipients'),'update'=>'#recipients', ))
		    ));
		    ?>
		</div>
		<?php echo $form->error($model, 'report_type'); ?>
                </div>

                <h3>Sensor information</h3>
                <div class="row">
		<?php echo $form->labelEx($model, 'gsn_id'); ?>
		<?php
		    $gsn_order = new CDbCriteria;
		    $gsn_order->order = 'gsn_name ASC';

		    echo $form->dropDownList($model, 'gsn_id', CHtml::listData(DiGsn::model()->with(array('diGsnPrivileges' => array('select' => false, 'joinType' => 'INNER JOIN', 'condition' => '"diGsnPrivileges".user_id = ' . Yii::app()->user->id)))->findAll($gsn_order), 'gsn_id', 'gsn_name'),
			    array(
				'empty' => 'Select',
				'options' => array($model->selectedGsn => array('selected' => 'selected')),
				'ajax' => array(
				    'type' => 'POST',
				    'url' => CController::createUrl('reportsDynamicSensors'),
				    'update' => '#UserReportsNewSubscription_' . 'sensor_id'
				)
			    )
		    );
		?>

		<?php echo $form->error($model, 'gsn_id'); ?>
                </div>

                <div class="row">
		<?php echo $form->labelEx($model, 'sensor_id'); ?>
		<?php
		    $sensor_order = new CDbCriteria;
		    $sensor_order->order = 'sensor_name ASC';
		    echo $form->dropDownList($model, 'sensor_id', /* array(), */CHtml::listData(DiSensors::model()->with(array('gsn' => array('select' => false, 'joinType' => 'INNER JOIN', 'condition' => '"gsn".gsn_id = ' . $model->selectedGsn)))->findAll($sensor_order), 'sensor_id', 'sensor_user_name'),
			    array(
				'empty' => 'Select',
				'options' => array($model->selectedSensor => array('selected' => 'selected')),
			    )
		    );
		?>
		<?php echo $form->error($model, 'sensor_id'); ?>
                </div>

                <div class="row">
		<?php echo $form->labelEx($model, 'email'); ?>
		<?php echo $form->textField($model, 'email'); ?>
		<?php echo $form->error($model, 'email'); ?>
                </div>


                <div class="row buttons">
		<?php echo CHtml::submitButton('Submit', array('name' => 'submit_button')); ?>
                </div>

	    <?php $this->endWidget(); ?>

		</div><!-- form -->
		<p>On the left side you can fill in the form to make a new subscription.</p>
	<?php
		    if ($model->newSubscription) {
			if ($model->successful) {
			    echo "Your subscription was successfuly saved. After administrator approves it you can consider your report subscription active!";
			    echo "<br/><br/>";
			} else {
			    echo "Your subscription was not successfuly saved. We advise you to contact our administrator or double check the data. Most likely this email already has the same report subscription, and it is not possible to make another!";
			    echo "<br/><br/>";
			}
		    }
	?>
    </div>
</div>