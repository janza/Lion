<?php
$this->pageTitle = Yii::app()->name . ' - Sensors';
$this->breadcrumbs = array(
    'Sensors',
);
?>
<div class="post">

    <h2 class="title">Sensor main page</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>You can see all sensors that are currently registered to all of the GSN servers you have privilege to work on.</p>

	<?php
	$rawData = Yii::app()->db->createCommand('SELECT s.*, g.gsn_name, g.gsn_url FROM di_sensors s JOIN di_gsn g on g.gsn_id = s.gsn_id JOIN di_gsn_privileges p on p.gsn_id = s.gsn_id and p.user_id = ' . Yii::app()->user->id . ' WHERE p.is_active = \'1\' ORDER BY p.gsn_id, s.sensor_id')->queryAll();
	// or using: $rawData=User::model()->findAll();
	$dataProvider = new CArrayDataProvider($rawData, array(
		    'keyField' => 'sensor_id',
		    'id' => 'sensor_id',
		    'sort' => array(
			'attributes' => array(
			    'sensor_user_name', 'location_y', 'location_x', 'gsn_url', 'gsn_name', 'is_active'
			),
		    ),
		    'pagination' => array(
			'pageSize' => 10,
		    ),
		));


	$this->widget('zii.widgets.grid.CGridView', array(
	    'id' => 'di-sensor-grid',
	    'dataProvider' => $dataProvider,
		'cssFile' => false,
		'itemsCssClass' => 'table table-striped table-hover',
	    //'filter'=>$model->model_view,
	    //'selectableRows'=>2,
	    'columns' => array(
		array('name' => 'sensor_user_name', 'header' => 'Sensor name'),
		array('name' => 'sensor_type', 'header' => 'Type'),
		array('name' => 'gsn_name', 'header' => 'GSN server name'),
		array('name' => 'location_x', 'header' => 'Latitude'),
		array('name' => 'location_y', 'header' => 'Longitude'),
		array(
		    'value' => '($data["is_active"] == 1 ? "Active" : "Deactivated")',
		    'name' => 'Activity',
		),
	    //'is_active',
	    /* 		array(
	      'class'=>'CLinkColumn',
	      'labelExpression'=>'($data["priv_exists"] == 1 ? "Revoke" : "Activation")',
	      'urlExpression'=>'Yii::app()->createUrl("site/gsn", array("gsn_id"=>$data["gsn_id"]))',
	      'header'=>'Activate',
	      ),
	     */
	    ),
	));
	?>
	<?php
	    $google = new googleMaps();

	    if ($google->allSensorsWithReadingsMap()){
	    //successful
	    }
	    else {
		//unsuccessful
	    }
	?>

    </div>
</div>
