<?php
$this->breadcrumbs=array(
	'Agg Day Hourlies'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List AggDayHourly', 'url'=>array('index')),
	array('label'=>'Manage AggDayHourly', 'url'=>array('admin')),
);
?>

<h1>Create AggDayHourly</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>