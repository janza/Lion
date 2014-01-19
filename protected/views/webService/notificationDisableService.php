<?php
$this->pageTitle=Yii::app()->name . ' - Notifications';
$this->breadcrumbs=array(
	'Notifications',
);
?>
<div class="post">
    <p class="date"><?php echo date("M"); ?><b><?php echo date("j"); ?></b></p>
    <h2 class="title">Notification deactivation reviewing</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>Time: <?php echo date("G:i:s"); ?></b></p>
	<p>Notifications for sensor <?echo $sensor->sensor_name ?> hosted on <?echo $gsn->gsn_url ?> are  <b>deactivated</b>!</p>

    </div>
</div>