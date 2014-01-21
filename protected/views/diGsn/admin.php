<?php
$this->breadcrumbs = array(
    'GSN Servers' => array('diGsn/index'),
    'Manage GSN',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('di-gsn-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<div class="post">

    <h2 class="title">Manage GSN servers</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>If you need further managing, proceed with following links:</p>
	<?php
	echo "<ul>";
	echo "<li>" . CHtml::link('Create DiGsn', array('diGsn/create')) . "</li>";
	echo "<li>" . CHtml::link('List DiGsn', array('diGsn/index')) . "</li>";
	echo "</ul>";
	?>
	<p>
	    You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
	    or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
	</p>

	<?php echo CHtml::link('Advanced Search', '#', array('class' => 'search-button')); ?>
	<div class="search-form" style="display:none">
	    <?php
	    $this->renderPartial('_search', array(
		'model' => $model,
	    ));
	    ?>
	</div><!-- search-form -->

	<?php
	    $this->widget('zii.widgets.grid.CGridView', array(
		'id' => 'di-gsn-grid',
		'dataProvider' => $model->search(),
		'filter' => $model,
		'columns' => array(
		    'gsn_id',
		    'gsn_name',
		    'gsn_url',
		    'city',
		    'state',
		    'last_change',
		    /*
		      'is_active',
		      'is_dummy',
		      'date_activated_id',
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
		     */
		    array(
			'class' => 'CButtonColumn',
		    ),
		),
	    ));
	?>
    </div>
</div>
