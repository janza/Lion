<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('reading_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->reading_id), array('view', 'id'=>$data->reading_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('gsn_id')); ?>:</b>
	<?php echo CHtml::encode($data->gsn_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('sensor_id')); ?>:</b>
	<?php echo CHtml::encode($data->sensor_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('unit_id')); ?>:</b>
	<?php echo CHtml::encode($data->unit_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('date_id')); ?>:</b>
	<?php echo CHtml::encode($data->date_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('hour')); ?>:</b>
	<?php echo CHtml::encode($data->hour); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('avg_value')); ?>:</b>
	<?php echo CHtml::encode($data->avg_value); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('max_value')); ?>:</b>
	<?php echo CHtml::encode($data->max_value); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('min_value')); ?>:</b>
	<?php echo CHtml::encode($data->min_value); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('amplitude')); ?>:</b>
	<?php echo CHtml::encode($data->amplitude); ?>
	<br />

	*/ ?>

</div>