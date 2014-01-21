<?php
$this->pageTitle = Yii::app()->name . ' - GSN managing';
$this->breadcrumbs = array(
    'GSN managing',
);
?>

<div class="post">

    <h2 class="title">System managing</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>Please choose one of the GSN servers below in order to show all the possibilities in passive heating control system.</p>
	<?php echo (isset($model->creatingMessage) ? $model->creatingMessage : "") ?>
	<div class="form">
	    <?php
	    $form = $this->beginWidget('CActiveForm', array(
			'id' => 'gsn_system_managing-form',
			'enableAjaxValidation' => false,
		    ));
	    ?>
	    <div class="row">
		<?php echo $form->labelEx($model, 'GSN server'); ?>
		<?php
		$gsn_order = new CDbCriteria;
		$gsn_order->order = 'gsn_name ASC';

		echo $form->dropDownList($model, 'gsn_id', CHtml::listData(DiGsn::model()->with(array('diGsnPrivileges' => array('select' => false, 'joinType' => 'INNER JOIN', 'condition' => '"diGsnPrivileges".user_id = ' . Yii::app()->user->id)))->findAll($gsn_order), 'gsn_id', 'gsn_name'),
			array(
			    'empty' => 'Select',
			    'options' => array($model->selectedGsn => array('selected' => 'selected')),
			    'ajax' => array(
				'type' => 'POST',
				'url' => CController::createUrl('managingPartialView'),
				'update' => '#system_control',
				'beforeSend' => 'function(){
				    document.body.style.cursor=\'wait\';
				}',
				'complete' => 'function(){
				    document.body.style.cursor=\'default\';
				}',
			    //'success'=>'js:function(){$("#managing").load(\'http://161.53.67.224'.Yii::app()->createUrl('systemManaging/managingPartialView').'\').fadeIn("slow");}',
			    )
			)
		);
		?>

		<?php echo $form->error($model, 'gsn_id'); ?>
	    </div>
	    <?php $this->endWidget(); ?>
    	    <div id="system_control">
    		<div id="managing">
		    <?php
		    if (isset($model->selectedGsn) || Yii::app()->user->selectedGsn != -99) {
			if (isset($model->selectedGsn)) {
			    $selected_gsn = $model->selectedGsn;
			    Yii::app()->user->setState('selectedGsn', $selected_gsn);
			}
			else
			    $selected_gsn = Yii::app()->user->selectedGsn;
			$model->liveDataOutput($selected_gsn);
		    }
		    ?>
		</div>
		<?php
		    if (isset($model->selectedGsn) || Yii::app()->user->selectedGsn != -99) {
			if (isset($model->selectedGsn)) {
			    $selected_gsn = $model->selectedGsn;
			    Yii::app()->user->setState('selectedGsn', $selected_gsn);
			}
			else
			    $selected_gsn = Yii::app()->user->selectedGsn;
			if ($model->gatherGsnConfigStats($selected_gsn, $return_message, $externalTempLimit, $internalTempLimit1, $internalTempLimit2, $internalTempLimit3, $fan_1, $fan_2, $fan_3, $fan_4, $fan_5, $heater_1, $heater_2, $heater_3, $heater_4, $heater_5, $rabbit_ip, $free_server_port, $auto_control, $manual_fan, $manual_heater, $air_intake, $emails))
			    $model->configTableOutput($externalTempLimit, $internalTempLimit1, $internalTempLimit2, $internalTempLimit3, $fan_1, $fan_2, $fan_3, $fan_4, $fan_5, $heater_1, $heater_2, $heater_3, $heater_4, $heater_5, $rabbit_ip, $free_server_port, $auto_control, $manual_fan, $manual_heater, $air_intake, $emails, $selected_gsn);
			else {
			    echo (isset($return_message) ? $return_message : "Unable to resolve the error while reading heating config file!<br/>");
			}
		    }
		?>
	    </div>
	</div>
    </div>
</div>
