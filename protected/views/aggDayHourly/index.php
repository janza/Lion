<?php
$this->breadcrumbs=array(
	'Agg Day Hourlies',
);

$this->menu=array(
	array('label'=>'Create AggDayHourly', 'url'=>array('create')),
	array('label'=>'Manage AggDayHourly', 'url'=>array('admin')),
);
?>

<h1>Agg Day Hourlies</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
