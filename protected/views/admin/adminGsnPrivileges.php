<?php
$this->pageTitle = Yii::app()->name . ' - GSN privileges';
$this->breadcrumbs = array(
    'Users' => array('prodUsers/index'),
    'GSN privileges',
);
?>
<div class="post">
    <p class="date"><?php echo date("M"); ?><b><?php echo date("j"); ?></b></p>
    <h2 class="title">GSN privileges administration</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>If you need further managing, proceed with following links:</p>
	<?php
	echo "<ul>";
	echo "<li>" . CHtml::link('User activity', array('prodUsers/userActivation')) . "</li>";
	echo "<li>" . CHtml::link('Create user', array('prodUsers/create')) . "</li>";
	echo "<li>" . CHtml::link('Manage users', array('prodUsers/admin')) . "</li>";
	echo "<li>" . CHtml::link('List users', array('prodUsers/index')) . "</li>";
	echo "</ul>";
	?>
	<p>You can enable or decline all user gsn privilege requests</p>
	<?php
	$rawData = Yii::app()->db->createCommand('SELECT g.*, u.username, u.first_name || \' \' || u.last_name as full_name, p.is_active as is_privilege_active, p.gsn_privilege_id FROM di_gsn g JOIN di_gsn_privileges p ON p.gsn_id = g.gsn_id JOIN prod_users u ON u.user_id = p.user_id ORDER BY p.user_id, g.gsn_id')->queryAll();
	// or using: $rawData=User::model()->findAll();
	$dataProvider = new CArrayDataProvider($rawData, array(
		    'keyField' => 'gsn_privilege_id',
		    'id' => 'gsn_privilege_id',
		    'sort' => array(
			'attributes' => array(
			    'username', 'full_name', 'gsn_name', 'city', 'state', 'gsn_url', 'is_active', 'is_privilege_active', 'gsn_privilege_id'
			),
		    ),
		    'pagination' => array(
			'pageSize' => 10,
		    ),
		));


	$this->widget('zii.widgets.grid.CGridView', array(
	    'id' => 'di-gsn-privileges-grid',
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
		    'header' => 'GSN name',
		    'name' => 'gsn_name'),
		array(
		    'header' => 'City',
		    'name' => 'city'),
		array(
		    'header' => 'GSN Url',
		    'name' => 'gsn_url'),
		array(
		    'name' => 'GSN activity',
		    'value' => '($data["is_active"]==1?"Active":"Inactive")'),
		array(
		    'name' => 'Privilege activness',
		    'value' => '($data["is_privilege_active"]==1?"Active":"Inactive")'),
		array(
		    'class' => 'CLinkColumn',
		    'labelExpression' => '($data["is_privilege_active"] == 1 ? "Revoke" : "Activate")',
		    'urlExpression' => 'Yii::app()->createUrl("admin/adminGsnPrivileges", array("gsn_privilege_id"=>$data["gsn_privilege_id"]))',
		    'header' => 'Activate',
		),
	    ),
	));
	/*
	  $this->widget('zii.widgets.grid.CGridView', array(
	  'id'=>'di-gsn-grid',
	  'dataProvider'=>$model->model_view->search_privileges(),
	  'filter'=>$model->model_view,
	  'selectableRows'=>2,
	  'columns'=>array(
	  array(
	  'class'=>'CCheckBoxColumn',
	  ),
	  //array( 'name' => 'is_active_privilege', 'value' => 'di_gsn_privileges.is_active'),
	  'gsn_name',
	  'city',
	  'state',
	  'gsn_url',
	  'is_active',
	  array('name' => 'is_active_privilege',
	  'value' => '($data->diGsnPrivileges->is_active_privilege is null ? "nista" : "ista")',
	  'type' => 'text'),
	  ),

	  )); */
	?>
    </div>
</div>