<?php
$this->breadcrumbs=array(
	'Agg Day Hourlies'=>array('index'),
	$model->reading_id,
);

$this->menu=array(
	array('label'=>'List AggDayHourly', 'url'=>array('index')),
	array('label'=>'Create AggDayHourly', 'url'=>array('create')),
	array('label'=>'Update AggDayHourly', 'url'=>array('update', 'id'=>$model->reading_id)),
	array('label'=>'Delete AggDayHourly', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->reading_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage AggDayHourly', 'url'=>array('admin')),
);
?>

<h1>View AggDayHourly #<?php echo $model->reading_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'reading_id',
		'gsn_id',
		'sensor_id',
		'unit_id',
		'date_id',
		'hour',
		'avg_value',
		'max_value',
		'min_value',
		'amplitude',
	),
)); ?>
