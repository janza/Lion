<?php
$this->breadcrumbs = array(
    'Sensors' => array('diSensors/index'),
    'Create sensor',
);
?>
<div class="post">

    <h2 class="title">Create sensor</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">

	<p>If you need further managing, proceed with following links:</p>
	<?php
	echo "<ul>";
	echo "<li>" . CHtml::link('Manage sensors', array('diSensors/admin')) . "</li>";
	echo "<li>" . CHtml::link('List sensors', array('diSensors/index')) . "</li>";
	echo "</ul>";
	?>

	<?php echo $this->renderPartial('_form', array('model' => $model)); ?>
    </div>
</div>
