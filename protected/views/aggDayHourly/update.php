<?php
$this->breadcrumbs=array(
	'Agg Day Hourlies'=>array('index'),
	$model->reading_id=>array('view','id'=>$model->reading_id),
	'Update',
);

$this->menu=array(
	array('label'=>'List AggDayHourly', 'url'=>array('index')),
	array('label'=>'Create AggDayHourly', 'url'=>array('create')),
	array('label'=>'View AggDayHourly', 'url'=>array('view', 'id'=>$model->reading_id)),
	array('label'=>'Manage AggDayHourly', 'url'=>array('admin')),
);
?>

<h1>Update AggDayHourly <?php echo $model->reading_id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>