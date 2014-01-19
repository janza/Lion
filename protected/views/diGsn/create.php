<?php
$this->breadcrumbs = array(
    'Di Gsns' => array('diGsn/index'),
    'Create GSN server',
);
?>

<div class="post">
    <p class="date"><?php echo date("M"); ?><b><?php echo date("j"); ?></b></p>
    <h2 class="title">Create new GSN</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>If you need further managing, proceed with following links:</p>
	<?php
	echo "<ul>";
	echo "<li>" . CHtml::link('Manage DiGsn', array('diGsn/admin')) . "</li>";
	echo "<li>" . CHtml::link('List DiGsn', array('diGsn/index')) . "</li>";
	echo "</ul>";
	?>
	    <?php echo $this->renderPartial('_form', array('model' => $model)); ?>
    </div>
</div>