<?php
$this->pageTitle = Yii::app()->name . ' - GSN administration';
$this->breadcrumbs = array(
    'GSN administration',
);
?>

<div class="post">
    <h2 class="title">GSN administration</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>All GSN servers that are currently in the system can be seen here.</p>

	<?php
	    $rawData = DiGsn::model()->queryAll();

	    foreach ($rawData as $gsn)
	    {
		echo '<div class="roundedBox" id="type1">';
		//when we have a round frame, we can print what we wanted
		echo "GSN id: ".$gsn->gsn_id.'<br/>';
		echo "GSN name: ".$gsn->gsn_name.'<br/>';
		echo "IP address: ".$gsn->gsn_ip.'<br/>';
		echo "Approved by administrator: ".(($report["is_active"]==1)?"Approved":"Waiting for approval").'<br/>';
		echo "Recipient: ".$report['email'].'<br/>';
		echo "Sending preferences: ".(($report["is_sending"])?"Sending":"Not sending").'<br/>';
		echo "Sending action: ".CHtml::link(($report["is_sending"])?"Stop":"Start",array('reportSubscription/userReportsSubscription', 'report_id'=>$report['report_id'],'type_int'=>$report['type_int'])).'<br/>';
		echo "Delete: ".CHtml::link('Delete',array('reportSubscription/userReportsSubscription', 'report_id'=>$report['report_id'],'type_int'=>$report['type_int'],'action'=>'delete')).'<br/>';
		echo '<div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div>';
		echo '</div>';
	    }
	?>

    </div>
</div>
