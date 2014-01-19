<?php
$this->pageTitle = Yii::app()->name . ' - Report subscriptions';
$this->breadcrumbs = array(
    'Report subscriptions',
);
?>

<div class="post">
    <p class="date"><?php echo date("M"); ?><b><?php echo date("j"); ?></b></p>
    <h2 class="title">Report subscription rewieving</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>Here user can view his/hers report subscriptions that are currently in the system</p>

	<?php
	$rawData = Yii::app()->db->createCommand('
	    SELECT n.*,u.username, u.first_name || \' \' || u.last_name as full_name, g.gsn_name, s.sensor_user_name
	    FROM
	    (
		select d.*, \'Daily\' as type, 1 as type_int
		from daily_reports d
		union all
		select m.*, \'Monthly\' as type, 2 as type_int
		from monthly_reports m
	    ) n
	    JOIN di_sensors s ON s.sensor_id = n.sensor_id
	    JOIN di_gsn g ON s.gsn_id = g.gsn_id
	    JOIN prod_users u ON u.user_id = n.user_id
	    WHERE u.user_id = ' . Yii::app()->user->id . '
	    order by type, g.gsn_name, s.sensor_name')->queryAll();

	foreach ($rawData as $report)
	{
	    echo '<div class="roundedBox" id="type1">';
	    //when we have a round frame, we can print what we wanted
	    echo "Report type: <b>".$report['type'].'</b><br/>';
	    echo "GSN server: <b>".$report['gsn_name'].'</b><br/>';
	    echo "Sensor name: <b>".$report['sensor_user_name'].'</b><br/>';
	    echo "Approved by administrator: <b>".(($report["is_active"]==1)?"Approved":"Waiting for approval").'</b><br/>';
	    echo "Recipient: <b>".$report['email'].'</b><br/>';

	    if ($report["is_active"]==1){
	    echo "Sending preferences: <b>".(($report["is_active"]==1)?(($report["is_sending"])?"Sending":"Not sending"):"Waiting for approval").'</b><br/>';
	    echo "Sending action: <b>".(($report["is_active"]==1)?CHtml::link(($report["is_sending"])?"Stop":"Start",array('reportSubscription/userReportsSubscription', 'report_id'=>$report['report_id'],'type_int'=>$report['type_int'])):"Waiting for approval").'</b><br/>';
	    }
	    echo "Delete: <b>".CHtml::link('Delete',array('reportSubscription/userReportsSubscription', 'report_id'=>$report['report_id'],'type_int'=>$report['type_int'],'action'=>'delete')).'</b><br/>';
	    echo '<div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div>';
	    echo '</div>';
	}
//	$dataProvider = new CArrayDataProvider($rawData, array(
//		    'keyField' => 'report_id',
//		    'id' => 'report_id',
//		    'sort' => array(
//			'attributes' => array(
//			    'type', 'report_id', 'full_name', 'sensor_user_name', 'report_id'
//			),
//		    ),
//		    'pagination' => array(
//			'pageSize' => 10,
//		    ),
//		));
//
//
//	$this->widget('zii.widgets.grid.CGridView', array(
//	    'id' => 'report_id-grid',
//	    'dataProvider' => $dataProvider,
//	    //'filter'=>$model->model_view,
//	    //'selectableRows'=>2,
//	    'columns' => array(
//		//array( 'name' => 'gsn_privilege_id'),
//		//'notification_id',
//		//'type',
//		array(
//		    'header' => 'Report type',
//		    'name' => 'type',
//		),
//		//'full_name',
//		array(
//		    'header' => 'GSN server',
//		    'name' => 'gsn_name',
//		),
//		//'sensor_user_name',
//		array(
//		    'header' => 'Sensor name',
//		    'name' => 'sensor_user_name',
//		),
//		//'is_active',
//		array(
//		    'value' => '($data["is_active"]==1)?"Approved":"Waiting for approval"',
//		    'name' => 'Approved by administrator',
//		),
//		//'is_sending',
//		//'email',
//		array(
//		    'header' => 'Recipient information',
//		    'name' => 'email',
//		),
//		array(
//		    'value' => '($data["is_sending"])?"Sending":"Not sending"',
//		    'name' => 'Sending preferences',
//		),
//		array(
//		    'class' => 'CLinkColumn',
//		    'labelExpression' => '($data["is_sending"])?"Stop":"Start"',
//		    'urlExpression' => 'Yii::app()->createUrl("reportSubscription/userReportsSubscription", array("report_id"=>$data["report_id"],"type_int"=>$data["type_int"]))',
//		    'header' => 'Sending',
//		),
//		array(
//		    'class' => 'CLinkColumn',
//		    'label' => 'Delete',
//		    'urlExpression' => 'Yii::app()->createUrl("reportSubscription/userReportsSubscription", array("report_id"=>$data["report_id"],"type_int"=>$data["type_int"],"action"=>\'delete\'))',
//		    'header' => 'Delete',
//		),
//	    ),
//	));
	?>
    </div>
</div>