<?php
$this->pageTitle=Yii::app()->name . ' - Watchdog timer requests';
$this->breadcrumbs=array(
	'Watchdog timer requests',
);
?>
<div class="post">
    <p class="date"><?php echo date("M"); ?><b><?php echo date("j"); ?></b></p>
    <h2 class="title">Watchdog reviewing</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>Here user can view his/hers watchdog timers that are currently in the system</p>
	<?php
	$rawData = Yii::app()->db->createCommand('SELECT n.*,u.username, u.first_name || \' \' || u.last_name as full_name, s.sensor_user_name
            FROM
            (
                select s.phone as recipient, s.watchdog_id, s.sensor_id, s.user_id, s.xml_name, s.critical_period, s.is_active, s.minimal_delay_between_emails, \'SMS\' as type
                from prod_sms_watchdog_timer s
                union all 
                select e.email as recipient, e.watchdog_id, e.sensor_id, e.user_id, e.xml_name, e.critical_period, e.is_active, e.minimal_delay_between_emails, \'E-mail\' as type
                from prod_email_watchdog_timer e
            ) n
            JOIN di_sensors s ON s.sensor_id = n.sensor_id
    JOIN prod_users u ON u.user_id = n.user_id WHERE u.user_id = ' . Yii::app()->user->id)->queryAll();
// or using: $rawData=User::model()->findAll();
	$dataProvider = new CArrayDataProvider($rawData, array(
		    'keyField' => 'watchdog_id',
		    'id' => 'watchdog_id',
		    'sort' => array(
			'attributes' => array(
			    'username', 'full_name', 'sensor_user_name', 'watchdog_id'
			),
		    ),
		    'pagination' => array(
			'pageSize' => 10,
		    ),
		));


	$this->widget('zii.widgets.grid.CGridView', array(
	    'id' => 'notification_id-grid',
	    'dataProvider' => $dataProvider,
	    //'filter'=>$model->model_view,
	    //'selectableRows'=>2,
	    'columns' => array(
		//array( 'name' => 'gsn_privilege_id'),
		//'notification_id',
		array('name' => 'type', 'header' => 'Watchdog type'),
		array('name' => 'sensor_user_name', 'header' => 'Sensor name'),
		//'sensor_user_name',
		//'xml_name',
		array('name' => 'critical_period', 'header' => 'Critical period'),
		//'critical_value',
		array('name' => 'minimal_delay_between_emails', 'header' => 'Resending interval'),
		//'resending_interval',
		array('name' => 'recipient', 'header' => 'Recipient'),
		//'email',
		array('value' => '($data["is_active"]==1)?"Active":"Inactive"', 'name' => 'Activity'),
		//'is_active',
		//'time_notification_asked',
		//array('name' => 'time_notification_approved', 'header' => 'Time of approval'),
		//'time_notification_approved',
		array(
		    'class' => 'CLinkColumn',
		    'label' => 'Delete',
		    'urlExpression' => 'Yii::app()->createUrl("user/userWatchdogRequests", array("watchdog_id"=>$data["watchdog_id"],"type"=>$data["type"],"action"=>\'delete\'))',
		    'header' => 'Delete',
		),
	    ),
	));
	?>
    </div>
</div>
