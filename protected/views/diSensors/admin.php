<?php
$this->breadcrumbs = array(
    'Sensors' => array('diSensors/index'),
    'Manage sensors',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('di-sensors-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<div class="post">

    <h2 class="title">Manage sensors</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">

	<p>If you need further managing, proceed with following links:</p>
	<?php
	echo "<ul>";
	echo "<li>" . CHtml::link('Create sensor', array('diSensors/create')) . "</li>";
	echo "<li>" . CHtml::link('List sensors', array('diSensors/index')) . "</li>";
	echo "</ul>";
	?>

	<h1>Manage Di Sensors</h1>

	<p>
	    You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
	    or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
	</p>

	<?php echo CHtml::link('Advanced Search', '#', array('class' => 'search-button')); ?>
	<div class="search-form" style="display:none">
	    <?php
	    $this->renderPartial('_search', array(
		'model' => $model,
	    ));
	    ?>
	</div><!-- search-form -->

	<?php
	    $this->widget('zii.widgets.grid.CGridView', array(
		'id' => 'di-sensors-grid',
		'dataProvider' => $model->search(),
		'filter' => $model,
		'columns' => array(
		    'sensor_id',
		    'sensor_name',
		    'sensor_user_name',
		    'gsn_id',
		    'sensor_type',
		    'location_x',
		    /*
		      'location_y',
		      'date_activated_id',
		      'date_deactivated_id',
		      'is_active',
		      'is_dummy',
		      'is_real_sensor',
		     */
		    array(
			'class' => 'CButtonColumn',
		    ),
		),
	    ));
	?>
    </div>
</div>
