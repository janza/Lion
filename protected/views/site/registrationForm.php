<?php
$this->pageTitle = Yii::app()->name . ' - Registration';
$this->breadcrumbs = array(
    'Registration',
);
?>
<div class="post">

    <h2 class="title">Welcome to Lion</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">

	<div class="roundedBox" id="type1">
	    <?php echo $this->renderPartial('_form', array('model' => $model)); ?>

	    <div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div>
	</div>
    </div>
</div>
