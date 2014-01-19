<?php
$this->breadcrumbs = array(
    'User activation',
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
	echo "<li>".  CHtml::link('<span>GSN privileges</span>', array('/admin/adminGsnPrivileges'))."</li>";
	echo "<li>" . CHtml::link('Create user', array('prodUsers/create')) . "</li>";
	echo "<li>" . CHtml::link('Manage users', array('prodUsers/admin')) . "</li>";
	echo "</ul>";
	?>

	<p>
	    <?php
		if (isset($model->username)) echo "<b>".$model->username."</b> with id ".$model->user_id." has been successfully managed!";
	    ?>
	</p>

	<?php
	$rawData = Yii::app()->db->createCommand('SELECT u.*, u.first_name || \' \' || u.last_name as full_name FROM prod_users u ORDER BY u.user_id')->queryAll();
	// or using: $rawData=User::model()->findAll();
	$dataProvider = new CArrayDataProvider($rawData, array(
		    'keyField' => 'user_id',
		    'id' => 'user_id',
		    'sort' => array(
			'attributes' => array(
			    'username', 'full_name', 'active'
			),
		    ),
		    'pagination' => array(
			'pageSize' => 10,
		    ),
		));


	$this->widget('zii.widgets.grid.CGridView', array(
	    'id' => 'user_id',
	    'dataProvider' => $dataProvider,
	    //'filter'=>$model->model_view,
	    //'selectableRows'=>2,
	    'columns' => array(
		//array( 'name' => 'gsn_privilege_id'),
		array(
		    'header' => 'Username',
		    'name' => 'username'),
		array(
		    'header' => 'Full name',
		    'name' => 'full_name'),
		array(
		    'header' => 'Email',
		    'name' => 'email'),
		array(
		    'header' => 'Phone',
		    'name' => 'phone'),
		array(
		    'name'=>'Role',
		    'value'=>'($data["group_id"]==1)?"Admin":"Regular user"',
		),
		array(
		    'class' => 'CLinkColumn',
		    'labelExpression' => '($data["group_id"] == 1 ? "Change to regular user" : "Change to administrator")',
		    'urlExpression' => 'Yii::app()->createUrl("prodUsers/userGroup", array("user_id"=>$data["user_id"]))',
		    'header' => 'Role changing',
		),
		array(
		    'class' => 'CLinkColumn',
		    'labelExpression' => '($data["active"] == 1 ? "Revoke" : "Activate")',
		    'urlExpression' => 'Yii::app()->createUrl("prodUsers/userActivation", array("user_id"=>$data["user_id"]))',
		    'header' => 'Activity',
		),
	    ),
	));
	?>
    </div>
</div>