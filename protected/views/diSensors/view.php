<?php
$this->breadcrumbs = array(
    'Sensors' => array('diSensors/index'),
    $model->sensor_id,
    'View',
);
?>
<div class="post">

    <h2 class="title">Sensor administration</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">

	<p>If you need further managing, proceed with following links:</p>
	<?php
	echo "<ul>";
	echo "<li>" . CHtml::link('Create sensors', array('diSensors/create')) . "</li>";
	echo "<li>" . CHtml::link('Manage sensors', array('diSensors/admin')) . "</li>";
	echo "<li>" . CHtml::link('List sensors', array('diSensors/index')) . "</li>";
	//echo "<li>" . CHtml::link('Delete sensors', array('diSensors/delete', 'id' => $model->gsn_id)) . "</li>";
	echo "<li>" . CHtml::link('Update sensors', array('diSensors/update', 'id' => $model->sensor_id)) . "</li>";
	echo "</ul>";
	?>
	<h1>View DiSensors #<?php echo $model->sensor_id; ?></h1>

	<?php
	$this->widget('zii.widgets.CDetailView', array(

        'cssFile' => false,
	    'data' => $model,
	    'attributes' => array(
    		'sensor_id',
    		'sensor_name',
    		'sensor_user_name',
    		'gsn_id',
    		'sensor_type',
    		'location_x',
    		'location_y',
    		'date_activated_id',
    		'date_deactivated_id',
    		'is_active',
    		'is_dummy',
    		'is_real_sensor',
	    ),
	));
	?>
    </div>
</div>
