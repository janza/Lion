<?php
$this->breadcrumbs = array(
    'Prod Users' => array('prodUsers/index'),
    'Create user',
);
?>

<div class="post">

    <h2 class="title">Create user</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">

	<p>If you need further managing, proceed with following links:</p>
	<?php
	echo "<ul>";
	echo "<li>" . CHtml::link('User activity', array('prodUsers/userActivation')) . "</li>";
	echo "<li>".  CHtml::link('<span>GSN privileges</span>', array('/admin/adminGsnPrivileges'))."</li>";
	echo "<li>" . CHtml::link('Manage users', array('prodUsers/admin')) . "</li>";
	echo "<li>" . CHtml::link('List users', array('prodUsers/index')) . "</li>";
	echo "</ul>";
	?>

	<?php echo $this->renderPartial('_form', array('model' => $model)); ?>
    </div>
</div>
