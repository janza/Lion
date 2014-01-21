<?php
$this->pageTitle = Yii::app()->name . ' - Reports managing';
$this->breadcrumbs = array(
    'Reports managing',
);
?>
<div class="post">

    <h2 class="title">User reports administration</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<?php
	    if (isset($model->message)) echo $model->message. "<br/>";
	?>
	<p>You can enable or decline all user report requests</p>

	<div id="partial_view">
	    <?php $this->renderPartial('_report_partial_view',array('model'=>$model,)); ?>
	</div>
    </div>
</div>
