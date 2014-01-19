<?php
$this->breadcrumbs = array(
    'Users' => array('prodUsers/index'),
    $model->user_id,
    'View',
);
?>
<div class="post">
    <p class="date"><?php echo date("M"); ?><b><?php echo date("j"); ?></b></p>
    <h2 class="title">User administration</h2>
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
	//echo "<li>" . CHtml::link('Delete user', array('prodUsers/delete', 'id' => $model->gsn_id)) . "</li>";
	echo "<li>" . CHtml::link('Update user', array('prodUsers/update', 'id' => $model->user_id)) . "</li>";
	echo "</ul>";
	?>
	<h1>View user #<?php echo $model->user_id; ?></h1>

	<?php
	$this->widget('zii.widgets.CDetailView', array(
	    'data' => $model,
	    'attributes' => array(
		'user_id',
		'first_name',
		'last_name',
		'email',
		'password',
		'salt',
		'group_id',
		'ip_address',
		'active',
		'created_on',
		'last_login',
		'username',
		'phone',
	    ),
	));
	?>
    </div>
</div>