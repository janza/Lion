<?php
$this->pageTitle = Yii::app()->name . ' - Reports';
$this->breadcrumbs = array(
    'Reports',
);
?>
<div class="post">
    <p class="date"><?php echo date("M"); ?><b><?php echo date("j"); ?></b></p>
    <h2 class="title">Main reports site</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<p>Here you can find all the sensor you have privilege to see. On the next page you will be able to chose from 2 different report types. Daily report, which consists of
	    the data from only one day. Monthly report, which consist of the data from a certain month. Both options also have ability to provide subscription. If you subscribe
	    to a sensor, you will get reports on daily/monthly basis.</p>
	<br/>
	<br/>

	<?php
	    $google = new googleMaps();

	    if ($google->allSensorsWithReadingsMap()){
	    //successful
	    }
	    else {
		//unsuccessful
	    }
	?>

    </div>
</div>