<?php
$this->pageTitle = Yii::app()->name . ' - Notification requests';
$this->breadcrumbs = array(
    'Notification requests',
);
?>
<div class="post">
    <p class="date"><?php echo date("M"); ?><b><?php echo date("j"); ?></b></p>
    <h2 class="title">Notification requests</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>Here user can view his/hers email and sms notifications that are currently in the system</p>
	<?php
	$rawData = Yii::app()->db->createCommand('SELECT n.*,u.username, u.first_name || \' \' || u.last_name as full_name, s.sensor_user_name
            FROM
            (
                select s.criteria_type, s.phone as recipient, s.notification_id, s.sensor_id, s.prod_user_id, s.xml_name, s.critical_value, s.is_active, s.resending_interval, \'SMS\' as type
                from prod_sms_notifications s
                union all 
                select e.criteria_type, e.email as recipient, e.notification_id, e.sensor_id, e.prod_user_id, e.xml_name, e.critical_value, e.is_active, e.resending_interval, \'E-mail\' as type
                from prod_email_notifications e
            ) n
            JOIN di_sensors s ON s.sensor_id = n.sensor_id
    JOIN prod_users u ON u.user_id = n.prod_user_id WHERE u.user_id = ' . Yii::app()->user->id)->queryAll();
// or using: $rawData=User::model()->findAll();
	$dataProvider = new CArrayDataProvider($rawData, array(
		    'keyField' => 'notification_id',
		    'id' => 'notification_id',
		    'sort' => array(
			'attributes' => array(
			    'username', 'full_name', 'sensor_user_name', 'notification_id'
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
		array('name' => 'type', 'header' => 'Type '),
		array('value' => '((strlen($data["sensor_user_name"])>18)?substr($data["sensor_user_name"],0,18)."...":$data["sensor_user_name"])', 'name' => 'Sensor name'),
		//'sensor_user_name',
		//'xml_name',
		array('name' => 'Critical value', 'value' => '($data["criteria_type"]==1?"below":"above")." ".$data["critical_value"]'),
		//'critical_value',
		array('name' => 'Resending interval', 'value' => '(int)($data["resending_interval"]/1000/60/60)."h ".(int)(($data["resending_interval"] - ((int)($data["resending_interval"]/1000/60/60))*60*60*1000)/1000/60)."m ".(($data["resending_interval"] - ((int)($data["resending_interval"]/1000/60))*60*1000)/1000)."s"'),
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
		    'urlExpression' => 'Yii::app()->createUrl("user/userNotificationRequests", array("notification_id"=>$data["notification_id"],"type"=>$data["type"],"action"=>\'delete\'))',
		    'header' => 'Delete',
		),
	    ),
	));
	?>
    </div>
</div>
