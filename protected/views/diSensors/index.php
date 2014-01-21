<?php
$this->breadcrumbs = array(
    'Sensors',
);
?>
<div class="post">

    <h2 class="title">Sensor table</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">

	<p>If you need further managing, proceed with following links:</p>
	<?php
	echo "<ul>";
	echo "<li>" . CHtml::link('Create sensors', array('diSensors/create')) . "</li>";
	echo "<li>" . CHtml::link('Manage sensors', array('diSensors/admin')) . "</li>";
	echo "</ul>";
	?>

	<?php
	$this->widget('zii.widgets.CListView', array(
	    'dataProvider' => $dataProvider,
	    'itemView' => '_view',
	));
	?>

    </div>
</div>
