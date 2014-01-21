<?php
$this->pageTitle = Yii::app()->name . ' - Error';
$this->breadcrumbs = array(
    'Error',
);
?>
<div class="post">

    <h2 class="title">Welcome to Lion</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<div class="roundedBox" id="type4">
	    <h2>Error <?php echo $code; ?></h2>

	    <div class="error">
		<?php echo CHtml::encode($message); ?>
	    </div>

	    <div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div>
	</div>
    </div>
</div>
