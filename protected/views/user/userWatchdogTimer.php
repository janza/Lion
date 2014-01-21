<?php
$this->pageTitle = Yii::app()->name . ' - Watchdog timers';
$this->breadcrumbs = array(
    'Watchdog timers',
);
?>
<div class="post">

    <h2 class="title">New watchdog timer</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<div class="roundedBox" id="type1">
	    <p>Fill in the form with data for your new notification</p>
	    <div class="form">
		<?php
		$form = $this->beginWidget('CActiveForm', array(
			    'id' => 'notification-form',
			    'enableClientValidation' => true,
			    //'enableAjaxValidation' => true,
			    'clientOptions' => array(
				'validateOnSubmit' => true,
			    ),
				'skin' => false
			));
		?>
		<p class="note">Fields with <span class="required">*</span> are required.</p>
		<?php echo $form->errorSummary($model); ?>

		<h3>Watchdog timer information</h3>

		<div style="border-top-style:solid;">
		<div class="row compactRadioGroup">
		    <?php echo $form->labelEx($model, 'watchdog_type'); ?>
			<?php
			echo $form->radioButtonList($model, 'watchdog_type',
				array(1 => 'SMS',
				    2 => 'E-mail',
				), array('separator' => "  ",
			   // 'onChange' => 'reload(this.form)',
			    'uncheckValue' => null,
				// 'onChange'=>CHtml::ajax(array('type'=>'POST', 'url'=>CController::createUrl('dynamicRecipients'),'update'=>'#recipients', ))
			));
			?>
		    <?php echo $form->error($model, 'watchdog_type'); ?>
    		</div>

    		<div class="row">
		    <?php echo $form->labelEx($model, 'critical_period'); ?>
		    <?php $model->critical_period = (isset($_GET['critical_period']) ? $_GET['critical_period'] : null);
			echo $form->textField($model, 'critical_period'); ?>&nbsp[<b>ms</b>]
		    <?php echo $form->error($model, 'critical_period'); ?>
    		</div>
    		<div class="row">
		    <?php echo $form->labelEx($model, 'minimal_delay_between_emails'); ?>
		    <?php $model->minimal_delay_between_emails = (isset($_GET['minimal_delay_between_emails']) ? $_GET['minimal_delay_between_emails'] : null);
			echo $form->textField($model, 'minimal_delay_between_emails'); ?>&nbsp[<b>ms</b>]
		    <?php echo $form->error($model, 'minimal_delay_between_emails'); ?>
    		</div>
		</div>

    		<h3>Sensor information</h3>
		<div style="border-top-style:solid;">
    		<div class="row">
		    <?php echo $form->labelEx($model, 'gsn_list'); ?>
		    <?php
			$gsn_order = new CDbCriteria;
			$gsn_order->order = 'gsn_name ASC';


			echo $form->dropDownList($model, 'gsn_list', CHtml::listData(DiGsn::model()->with(array('diGsnPrivileges' => array('select' => false, 'joinType' => 'INNER JOIN', 'condition' => '"diGsnPrivileges".user_id = ' . Yii::app()->user->id)))->findAll($gsn_order), 'gsn_id', 'gsn_name'),
				array(
				    'empty' => 'Select',
				    'ajax' => array(
					'type' => 'POST',
					'url' => CController::createUrl('user/WatchdogDynamicSensors'),
					'update' => '#UserWatchdogTimer_' . 'sensor_list'
				    )
				)
			);
		    ?>

		    <?php echo $form->error($model, 'gsn_list'); ?>
    		</div>

    		<div class="row">
		    <?php echo $form->labelEx($model, 'sensor_list'); ?>
		    <?php
			$sensor_order = new CDbCriteria;
			$sensor_order->order = 'sensor_name ASC';
			echo $form->dropDownList($model, 'sensor_list', array(), //CHtml::listData(DiSensors::model()->findAll($sensor_order),'sensor_id','sensor_user_name'),
				array(
				    'empty' => 'Select',
				    'ajax' => array(
					'type' => 'POST',
					//'url' => CController::createUrl('user/notificationsDynamicUnits'),
					//'update' => '#UserNotifications_' . 'measuring_unit_list'
				    )
				)
			);
		    ?>
		    <?php echo $form->error($model, 'sensor_list'); ?>
    		</div>

		</div>
		<div id="email">
		<?php //$this->renderPartial('_notification_recipients', array('model'=> $model,'form'=>$form));   ?>
		<?php //if ($model->watchdog_type == 1) : ?>
	    		<h3>Recipients</h3>
			<div style="border-top-style:solid;">
	    		<p>If you want to enter multiple recipients here, separate them with comma (<b>,</b>)</p>
	    		<p>Example: your.email@company.com, your.college.email@companytwo.com</p>
	    		<div class="row">
		    <?php echo $form->labelEx($model, 'email'); ?>
		    <?php echo $form->textField($model, 'email'); ?>
		    <?php //echo $form->error($model,'email'); ?>
			</div>
			</div>
		</div>
		<?php //endif; ?>
		<div id="phone">
		<?php //if ($model->watchdog_type == 2) : ?>
				<h3>Recipients</h3>
				<div style="border-top-style:solid;">
				<p>If you want to enter multiple recipients here, separate them with comma (<b>,</b>)</p>
				<p>Example: +38599009991, +38511001119</p>
				<div class="row">
		    <?php echo $form->labelEx($model, 'phone_number'); ?>
		    <?php echo $form->textField($model, 'phone_number'); ?>
		    <?php //echo $form->error($model,'phone_number');  ?>
	    		</div>
				</div>
		<?php //endif; ?>
		</div>


<div style="border-top-style:solid;">
		<?php if (CCaptcha::checkRequirements()): ?>
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
		    <?php echo CHtml::submitButton('Submit'); ?>
				</div>
		<?php endif; ?>
</div>

		<?php $this->endWidget(); ?>
		<div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div>

	    </div>
	</div><!-- form -->
    </div>
</div>

