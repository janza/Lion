<?php
$this->breadcrumbs = array(
    'GSN Servers' => array('index'),
    $model->gsn_id,
    'View GSN',
);
/*
  $this->menu=array(
  array('label'=>'List DiGsn', 'url'=>array('index')),
  array('label'=>'Create DiGsn', 'url'=>array('create')),
  array('label'=>'Update DiGsn', 'url'=>array('update', 'id'=>$model->gsn_id)),
  array('label'=>'Delete DiGsn', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->gsn_id),'confirm'=>'Are you sure you want to delete this item?')),
  array('label'=>'Manage DiGsn', 'url'=>array('admin')),
  ); */
?>

<div class="post">
    <p class="date"><?php echo date("M"); ?><b><?php echo date("j"); ?></b></p>
    <h2 class="title">GSN administration</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">

	<p>If you need further managing, proceed with following links:</p>
	<?php
	echo "<ul>";
	echo "<li>" . CHtml::link('Create DiGsn', array('diGsn/create')) . "</li>";
	echo "<li>" . CHtml::link('Manage DiGsn', array('diGsn/admin')) . "</li>";
	echo "<li>" . CHtml::link('List DiGsn', array('diGsn/index')) . "</li>";
	//echo "<li>" . CHtml::link('Delete DiGsn', array('diGsn/delete', 'id' => $model->gsn_id)) . "</li>";
	echo "<li>" . CHtml::link('Update DiGsn', array('diGsn/update', 'id' => $model->gsn_id)) . "</li>";
	echo "</ul>";
	?>

	<h1>View DiGsn #<?php echo $model->gsn_id; ?></h1>

	<?php
	$this->widget('zii.widgets.CDetailView', array(
	    'data' => $model,
	    'attributes' => array(
		'gsn_id',
		'gsn_name',
		'gsn_url',
		'city',
		'state',
		'is_active',
		'date_deactivated_id',
		'username',
		'password',
		'gsn_ip',
		'gsn_port',
		'port_ssl',
		'database_schema',
		'database_user',
		'database_password',
		'database_port',
		'sftp_username',
		'sftp_password',
		'notification_folder',
		'notification_backup_folder',
		'gsn_home_folder',
	    /*
	      'last_change',
	      'is_dummy',
	      'date_activated_id',
	     */
	    ),
	));
	?>

    </div>
</div>