<?php
$this->breadcrumbs = array(
    'GSN Servers',
);
?>
<div class="post">

    <h2 class="title">GSN server table</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>If you need further managing, proceed with following links:</p>
	<?php
	echo "<ul>";
	echo "<li>" . CHtml::link('Create DiGsn', array('diGsn/create')) . "</li>";
	echo "<li>" . CHtml::link('Manage DiGsn', array('diGsn/admin')) . "</li>";
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
