<?php
$this->pageTitle = Yii::app()->name . ' - Watchdog timers managing';
$this->breadcrumbs = array(
    'Watchdog timers managing',
);
?>
<div class="post">

    <h2 class="title">Watchdog timers administrations</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>You can enable or decline all user SMS and E-mail watchdog timer requests</p>

	<?php
	$rawData = Yii::app()->db->createCommand('SELECT n.*,u.username, u.first_name || \' \' || u.last_name as full_name, s.sensor_user_name
            FROM
            (
                select s.phone as recipient, s.watchdog_id, s.sensor_id, s.user_id, s.xml_name, s.critical_period, s.is_active, s.minimal_delay_between_emails,s.time_watchdog_approved, \'SMS\' as type
                from prod_sms_watchdog_timer s
                union all
                select e.email as recipient, e.watchdog_id, e.sensor_id, e.user_id, e.xml_name, e.critical_period, e.is_active, e.minimal_delay_between_emails,e.time_watchdog_approved, \'E-mail\' as type
                from prod_email_watchdog_timer e
            ) n
            JOIN di_sensors s ON s.sensor_id = n.sensor_id
            JOIN prod_users u ON u.user_id = n.user_id')->queryAll();
	// or using: $rawData=User::model()->findAll();
	$dataProvider = new CArrayDataProvider($rawData, array(
		    'keyField' => 'watchdog_id',
		    'id' => 'watchdog_id',
		    'sort' => array(
			'attributes' => array(
			    'type', 'username', 'full_name', 'sensor_user_name', 'watchdog_id'
			),
		    ),
		    'pagination' => array(
			'pageSize' => 10,
		    ),
		));

	foreach ($rawData as $notification)
	{
	    echo '<div class="roundedBox" id="type1">';
	    //when we have a round frame, we can print what we wanted
	    echo "Watchdog ID: ".$notification['watchdog_id'].'<br/>';
	    echo "Watchdog type: ".$notification['type'].'<br/>';
	    echo "Sensor name: ".$notification['sensor_user_name'].'<br/>';
	    echo "XML name: ".$notification["xml_name"].'<br/>';
	    echo "Critical period: ".$notification["critical_period"].'<br/>';
	    echo "Minimal delay between emails: ".$notification["minimal_delay_between_emails"].'<br/>';
	    echo "Recipient: ".$notification['recipient'].'<br/>';
	    echo "Time of approval: ".$notification['time_watchdog_approved'].'<br/>';
	    echo "Activity: ".(($notification["is_active"]=="1")?"Active":"Inactive").'<br/>';
	    echo "Activation: ".CHtml::link(($notification["is_active"] == 1) ? "Revoke" : "Activate",array('admin/adminWatchdogRequests', "watchdog_id"=>$notification["watchdog_id"], "type"=>$notification["type"])).'<br/>';
	    echo "Delete: ".CHtml::link('Delete',array('admin/adminWatchdogRequests', "watchdog_id"=>$notification["watchdog_id"],"type"=>$notification["type"],"action"=>"delete")).'<br/>';
	    echo '<div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div>';
	    echo '</div>';
	}
/*
	$this->widget('zii.widgets.grid.CGridView', array(
	    'id' => 'notification_id-grid',
	    'dataProvider' => $dataProvider,
	    //'filter'=>$model->model_view,
	    //'selectableRows'=>2,
	    'columns' => array(
		//array( 'name' => 'gsn_privilege_id'),
		array(
		    'name' => 'notification_id',
		    'header' => 'ID',
		),
		array(
		    'name' => 'type',
		    'header' => 'Notification type',
		),
		array(
		    'name' => 'sensor_user_name',
		    'header' => 'Sensor name',
		),
		array(
		    'name' => 'xml_name',
		    'header' => 'XML name',
		),
		array(
		    'name' => 'critical_value',
		    'header' => 'Critical value',
		),
		array(
		    'name' => 'resending_interval',
		    'header' => 'Resending interval',
		),
		array(
		    'name' => 'recipient',
		    'header' => 'Recipient',
		),
		array(
		    'name' => 'Activity',
		    'value' => '($data["is_active"]=="1")?"Active":"Inactive"',
		),
		//'is_active',
		//'time_notification_asked',
		array(
		    'name' => 'time_notification_approved',
		    'header' => 'Time of approval',
		),
		array(
		    'class' => 'CLinkColumn',
		    'labelExpression' => '($data["is_active"] == 1 ? "Revoke" : "Activate")',
		    'urlExpression' => 'Yii::app()->createUrl("admin/adminNotificationRequests", array("notification_id"=>$data["notification_id"], "type"=>$data["type"]))',
		    'header' => 'Activate',
		),
		array(
		    'class' => 'CLinkColumn',
		    'label' => 'Delete',
		    'urlExpression' => 'Yii::app()->createUrl("admin/adminNotificationRequests", array("notification_id"=>$data["notification_id"],"type"=>$data["type"],"action"=>\'delete\'))',
		    'header' => 'Delete',
		),
	    ),
	));
*/
	?>

    </div>
</div>
