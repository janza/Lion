<?php
$this->breadcrumbs = array(
    'Users',
);
?>

<div class="post">
    <p class="date"><?php echo date("M"); ?><b><?php echo date("j"); ?></b></p>
    <h2 class="title">Users table</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">

	<p>If you need further managing, proceed with following links:</p>
	<?php
	echo "<ul>";
	echo "<li>" . CHtml::link('User activity', array('prodUsers/userActivation')) . "</li>";
	echo "<li>".  CHtml::link('<span>GSN privileges</span>', array('/admin/adminGsnPrivileges'))."</li>";
	echo "<li>" . CHtml::link('Create user', array('prodUsers/create')) . "</li>";
	echo "<li>" . CHtml::link('Manage users', array('prodUsers/admin')) . "</li>";
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