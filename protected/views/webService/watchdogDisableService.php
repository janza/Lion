<?php
$this->pageTitle=Yii::app()->name . ' - Watchdog timer requests';
$this->breadcrumbs=array(
	'Watchdog timer requests',
);
?>
<div class="post">
    <p class="date"><?php echo date("M"); ?><b><?php echo date("j"); ?></b></p>
    <h2 class="title">Watchdog deactivation reviewing</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>Watchdog timer for sensor <?echo $sensor->sensor_name ?> hosted on <?echo $gsn->gsn_url ?> <b>deactivated</b>!</p>
	
    </div>
</div>
