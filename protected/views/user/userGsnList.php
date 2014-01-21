<?php
$this->pageTitle = Yii::app()->name . ' - GSN';
$this->breadcrumbs = array(
    'GSN',
);
?>
<div class="post">

    <h2 class="title">GSN servers list</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>You can see all the GSN servers currently in the system. The last column gives you information whether you have already request for your privilege on this GSN server or not.</p>
	<p>Subscribing to a GSN server gives you access to all the sensors that GSN currently provides. This request will first be approved by our administrator.</p>
	<p>For any questions please go on contact site.</p>

	<?php
	$rawData = Yii::app()->db->createCommand('SELECT g.*, case when p.gsn_id is not null then 1 else 0 end as priv_exists, case when p.gsn_id is not null then p.is_active else \'0\' end as is_active_priv FROM di_gsn g LEFT JOIN di_gsn_privileges p on p.gsn_id = g.gsn_id and p.user_id = ' . Yii::app()->user->id . " WHERE g.is_dummy = '0' ORDER BY g.gsn_id")->queryAll();
	// or using: $rawData=User::model()->findAll();
	$dataProvider = new CArrayDataProvider($rawData, array(
		    'keyField' => 'gsn_id',
		    'id' => 'gsn_id',
		    'sort' => array(
			'attributes' => array(
			    'gsn_name', 'city', 'state', 'gsn_url', 'is_active'
			),
		    ),
		    'pagination' => array(
			'pageSize' => 10,
		    ),
		));


	$this->widget('zii.widgets.grid.CGridView', array(
	    'id' => 'di-gsn-grid',
	    'dataProvider' => $dataProvider,
	    //'filter'=>$model->model_view,
	    //'selectableRows' => 2,
	    'columns' => array(
		array('header' => 'GSN name',
		    'name' => 'gsn_name'),
		array('header' => 'City', 'name' => 'city'),
		array('header' => 'State', 'name' => 'state'),
		array('class' => 'CLinkColumn', 'header' => 'GSN url', 'labelExpression' => '$data["gsn_url"]', 'urlExpression' => '$data["gsn_url"]'),
		array('header' => 'GSN activity', 'value' => '$data["is_active"] == 1 ? "Active" : "Inactive"'),
		array('header' => 'Privilege given', 'value' => '(($data["priv_exists"] == 1) ? (($data["is_active_priv"] == 1) ? "Yes" : "No") : "No privilege request")'),
		array(
		    'class' => 'CLinkColumn',
		    'labelExpression' => '($data["priv_exists"] == 1 ? "Decline" : "Request")',
		    'urlExpression' => 'Yii::app()->createUrl("user/userGsnList", array("gsn_id"=>$data["gsn_id"]))',
		    'header' => 'Privilege request',
		),
	    ),
	));
	?>
    </div>
</div>
