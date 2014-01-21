<?php
$this->breadcrumbs = array(
    'GSN Servers' => array('diGsn/index'),
    'Update GSN',
);
?>

<div class="post">

    <h2 class="title">Update GSN server, <?php echo $model->gsn_id; ?></h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>If you need further managing, proceed with following links:</p>
	<?php
	echo "<ul>";
	echo "<li>" . CHtml::link('Create DiGsn', array('diGsn/create')) . "</li>";
	echo "<li>" . CHtml::link('Manage DiGsn', array('diGsn/admin')) . "</li>";
	echo "<li>" . CHtml::link('List DiGsn', array('diGsn/index')) . "</li>";
	echo "<li>" . CHtml::link('View DiGsn', array('diGsn/view','id'=>$model->gsn_id)) . "</li>";
	echo "</ul>";
	?>
	<?php echo $this->renderPartial('_form', array('model' => $model)); ?>

    </div>
</div>
