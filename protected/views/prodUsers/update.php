<?php
$this->breadcrumbs = array(
    'Users' => array('prodUsers/index'),
    'Update user',
);
?>

<div class="post">

    <h2 class="title">Update user, <?php echo $model->user_id; ?></h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>If you need further managing, proceed with following links:</p>
	<?php
	echo "<ul>";
	echo "<li>" . CHtml::link('User activity', array('prodUsers/userActivation')) . "</li>";
	echo "<li>".  CHtml::link('<span>GSN privileges</span>', array('/admin/adminGsnPrivileges'))."</li>";
	echo "<li>" . CHtml::link('Create user', array('prodUsers/create')) . "</li>";
	echo "<li>" . CHtml::link('Manage users', array('prodUsers/admin')) . "</li>";
	echo "<li>" . CHtml::link('List users', array('prodUsers/index')) . "</li>";
	echo "<li>" . CHtml::link('View user', array('prodUsers/view', 'id' => $model->user_id)) . "</li>";
	echo "</ul>";
	?>
	<h1>Update user <?php echo $model->user_id; ?></h1>

	<?php echo $this->renderPartial('_form', array('model' => $model)); ?>

    </div>
</div>
