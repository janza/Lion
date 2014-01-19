<?php
	$rawData = Yii::app()->db->createCommand('SELECT n.*,u.username, u.first_name || \' \' || u.last_name as full_name, s.sensor_user_name, g.gsn_name, g.city, g.state FROM
        (select d.*, \'Daily\' as type, 1 as type_int from daily_reports d union all select m.*, \'Monthly\' as type, 2 as type_int from monthly_reports m) n
        JOIN di_sensors s ON s.sensor_id = n.sensor_id
        JOIN di_gsn g on n.gsn_id = g.gsn_id
        JOIN prod_users u ON u.user_id = n.user_id
	ORDER BY u.username, u.first_name || \' \' || u.last_name, type, gsn_name, sensor_user_name')->queryAll();
// or using: $rawData=User::model()->findAll();
	$dataProvider = new CArrayDataProvider($rawData, array(
		    'keyField' => 'report_id',
		    'id' => 'report_id',
		    'sort' => array(
			'attributes' => array(
			    'username', 'full_name', 'gsn_name', 'is_active'
			),
		    ),
		    'pagination' => array(
			'pageSize' => 10,
		    ),
		));


	$this->widget('zii.widgets.grid.CGridView', array(
	    'id' => 'report_id',
	    'dataProvider' => $dataProvider,
	    //'filter'=>$model->model_view,
	    //'selectableRows'=>2,
	    'columns' => array(
		//array( 'name' => 'gsn_privilege_id'),
		//'username',
		array(
		    'name' => 'report_id',
		    'header' => 'ID',
		),
		array(
		    'name' => 'username',
		    'header' => 'User name',
		),
		//'full_name',
		array(
		    'name' => 'full_name',
		    'header' => 'Full name',
		),
		//'gsn_name',
		array(
		    'name' => 'gsn_name',
		    'header' => 'GSN name',
		),
		array(
		    'name' => 'sensor_user_name',
		    'header' => 'Sensor name',
		),
		//'type',
		array(
		    'name' => 'type',
		    'header' => 'Type',
		),
		array(
		    'name' => 'email',
		    'header' => 'Recipient',
		),
		array(
		    'name' => 'Sending preferences',
		    'value' => '(($data["is_active"]==1)?(($data["is_sending"]==1)?"Sending":"Not sending"):"Inactive")'
		),
		array(
		    'class' => 'CLinkColumn',
		    'labelExpression' => '(($data["is_sending"] == 1) ? "Stop" : "Start")',
		    'urlExpression' => 'Yii::app()->createUrl("admin/adminReportsManaging", array("report_id"=>$data["report_id"], "type_int"=>$data["type_int"] ,"sending"=>"true"))',
		    'header' => 'Sending',
		),
		array('name' => 'Report activity',
		    'value' => '(($data["is_active"]==1)?"Active":"Inactive")'
		),
		array(
		    'class' => 'CLinkColumn',
		    'labelExpression' => '(($data["is_active"] == 1) ? "Decline" : "Approve")',
		    'urlExpression' => 'Yii::app()->createUrl("admin/adminReportsManaging", array("report_id"=>$data["report_id"], "type_int"=>$data["type_int"] ,"request"=>"true"))',
		    'header' => 'Activation',
		),
		array(
		    'class' => 'CLinkColumn',
		    'label' => 'Delete',
		    'urlExpression' => 'Yii::app()->createUrl("admin/adminReportsManaging", array("report_id"=>$data["report_id"], "type_int"=>$data["type_int"] ,"delete"=>"true"))',
		    'header' => 'Delete',
		),
	    ),
	));
	?>