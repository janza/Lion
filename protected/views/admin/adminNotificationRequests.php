<?php
$this->pageTitle = Yii::app()->name . ' - Notification managing';
$this->breadcrumbs = array(
    'Notification managing',
);
?>
<div class="post">
    <p class="date"><?php echo date("M"); ?><b><?php echo date("j"); ?></b></p>
    <h2 class="title">Notification administrations</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>You can enable or decline all user SMS and E-mail notification requests</p>

	<?php
	$rawData = Yii::app()->db->createCommand('SELECT n.*,u.username,g.gsn_name, un.unit_mark, u.first_name || \' \' || u.last_name as full_name, s.sensor_user_name
            FROM
            (
                select s.gsn_id, s.unit_id, s.criteria_type,s.phone as recipient, s.notification_id, s.sensor_id, s.prod_user_id, s.xml_name, s.critical_value, s.is_active, s.resending_interval,s.time_notification_approved, \'SMS\' as type
                from prod_sms_notifications s
                union all 
                select e.gsn_id, e.unit_id, e.criteria_type,e.email as recipient, e.notification_id, e.sensor_id, e.prod_user_id, e.xml_name, e.critical_value, e.is_active, e.resending_interval,e.time_notification_approved, \'E-mail\' as type
                from prod_email_notifications e
            ) n
            JOIN di_sensors s ON s.sensor_id = n.sensor_id
            JOIN prod_users u ON u.user_id = n.prod_user_id
	    JOIN di_units un ON un.unit_id = n.unit_id
	    JOIN di_gsn g ON g.gsn_id = n.gsn_id')->queryAll();
	// or using: $rawData=User::model()->findAll();
	$dataProvider = new CArrayDataProvider($rawData, array(
		    'keyField' => 'notification_id',
		    'id' => 'notification_id',
		    'sort' => array(
			'attributes' => array(
			    'type', 'username', 'full_name', 'sensor_user_name', 'notification_id'
			),
		    ),
		    'pagination' => array(
			'pageSize' => 10,
		    ),
		));

//	foreach ($rawData as $notification)
//	{
//	    echo '<div class="roundedBox" id="type1">';
//	    //when we have a round frame, we can print what we wanted
//	    echo "Notification information: <b>ID ".$notification['notification_id'].", XML name: ".$notification["xml_name"].'</b><br/>';
//	    echo "Notification type: ".$notification['type'].'<br/>';
//	    echo "User information: <b>".$notification['full_name'].", recipients: ".$notification['recipient'].'</b><br/>';
//	    echo "Sending criteria: <b>Send when ".($notification['criteria_type']==1?"below":"above")." ".$notification["critical_value"].$notification["unit_mark"]."</b><br/>";
//	    echo "Sensor information: <b>Sensor ".$notification['sensor_user_name'].", on GSN ".$notification['gsn_name'].'</b><br/>';
//	    echo "Resending interval: ".$notification["resending_interval"].'<br/>';
//	    //echo "Time of approval: ".$notification['time_notification_approved'].'<br/>';
//	    echo "Activity: ".(($notification["is_active"]=="1")?"Active":"Inactive").'<br/>';
//	    echo "Activation: ".CHtml::link(($notification["is_active"] == 1) ? "Revoke" : "Activate",array('admin/adminNotificationRequests', "notification_id"=>$notification["notification_id"], "type"=>$notification["type"])).'<br/>';
//	    echo "Delete: ".CHtml::link('Delete',array('admin/adminNotificationRequests', "notification_id"=>$notification["notification_id"],"type"=>$notification["type"],"action"=>"delete")).'<br/>';
//	    echo '<div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div>';
//	    echo '</div>';
//	}

	$this->widget('zii.widgets.CListView', array(
	    'dataProvider' => $dataProvider,
	    'itemView' => '_notification_view',
	));
	?>

    </div>
</div>