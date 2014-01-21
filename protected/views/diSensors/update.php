<?php
$this->breadcrumbs = array(
    'Sensors' => array('diSensors/index'),
    'Update sensor',
);
?>
<div class="post">

    <h2 class="title">Update sensor, <?php echo $model->sensor_id; ?></h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>If you need further managing, proceed with following links:</p>
	<?php
	echo "<ul>";
	echo "<li>" . CHtml::link('Create sensors', array('diSensors/create')) . "</li>";
	echo "<li>" . CHtml::link('Manage sensors', array('diSensors/admin')) . "</li>";
	echo "<li>" . CHtml::link('List sensors', array('diSensors/index')) . "</li>";
	echo "<li>" . CHtml::link('View sensors', array('diSensors/view', 'id' => $model->sensor_id)) . "</li>";
	echo "</ul>";
	?>
	<h1>Update DiSensors <?php echo $model->sensor_id; ?></h1>

	<?php echo $this->renderPartial('_form', array('model' => $model)); ?>

    </div>
</div>
