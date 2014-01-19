<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="language" content="en" />

        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
        <link href="http://fonts.googleapis.com/css?family=Oswald:400,700" rel="stylesheet" type="text/css" />
        <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all" />
		<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css" media="all" />

        <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/default_after_login.css" rel="stylesheet" type="text/css" media="all" />

        <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/protected/javascript/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/protected/javascript/jquery.dropotron-1.0.js"></script>
        <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/protected/javascript/init.js"></script>
        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    </head>

    <body>
<div id="header-wrapper">
	    <div id="header">
		<div id="logo">
		    <h1><a href="#">Lion</a></h1>
		    <p><?php if (!Yii::app()->user->isGuest) echo "Welcome " . Yii::app()->user->name; ?></p>

	<div id="menu-wrapper">
	    <div id="menu-content">
		<ul id="menu">
		    <?php if (Yii::app()->user->isGuest) : ?>
			<li <?php if ($this->breadcrumbs[0] == "Home") echo 'class="first"' ?>><?php echo CHtml::link('<span>Home</span>', array('/site/index')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Contact") echo 'class="first"' ?>><?php echo CHtml::link('<span>Contact</span>', array('/site/contact')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Login") echo 'class="first"' ?>><?php echo CHtml::link('<span>Login</span>', array('/site/login')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Registration") echo 'class="first"' ?>><?php echo CHtml::link('<span>Registration</span>', array('/site/registrationForm')); ?></li>
		    <?php elseif (Yii::app()->user->group==1) : ?>
			<li <?php if ($this->breadcrumbs[0] == "") echo 'class="first"' ?>><?php echo CHtml::link('<span>Home</span>', array('/user/userPersonal')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Home") echo 'class="first"' ?>><?php echo CHtml::link('<span>Admin</span>', array('/admin/index')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Users"
				|| $this->breadcrumbs[0] == "Update user"
				|| $this->breadcrumbs[0] == "Manage users"
				|| $this->breadcrumbs[0] == "View user"
				|| $this->breadcrumbs[0] == "Create user"
				|| $this->breadcrumbs[0] == "GSN privileges"
				|| $this->breadcrumbs[0] == "User activation") echo 'class="first"' ?>><?php echo CHtml::link('<span>Users</span>', array('/prodUsers/index')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "GSN Servers"
				|| $this->breadcrumbs[0] == "Update GSN"
				|| $this->breadcrumbs[0] == "Manage GSN"
				|| $this->breadcrumbs[0] == "View GSN"
				|| $this->breadcrumbs[0] == "Create GSN server") echo 'class="first"' ?>><?php echo CHtml::link('<span>GSN</span>', array('/diGsn/index')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Reports managing") echo 'class="first"' ?>><?php echo CHtml::link('<span>Reports managing</span>', array('/admin/adminReportsManaging')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Notification managing") echo 'class="first"' ?>><?php echo CHtml::link('<span>Notification managing</span>', array('/admin/adminNotificationRequests')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Watchdog timers managing") echo 'class="first"' ?>><?php echo CHtml::link('<span>Watchdog managing</span>', array('/admin/adminWatchdogRequests')); ?></li>



			<li <?php if ($this->breadcrumbs[0] == "Sensors"
				|| $this->breadcrumbs[0] == "Update sensor"
				|| $this->breadcrumbs[0] == "Manage sensors"
				|| $this->breadcrumbs[0] == "View sensor")echo 'class="first"' ?>><?php echo CHtml::link('<span>Sensors</span>', array('/diSensors/index')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Logout") echo 'class="first"' ?>><?php echo CHtml::link('<span>Logout</span>', array('/site/logout')); ?></li>
		    <?php endif; ?>
		</ul>
	    </div>	</div>
		</div>
	    </div>
	</div>

	<?php if ((isset($this->breadcrumbs[0])?$this->breadcrumbs[0]:"") == "Update GSN"
		|| (isset($this->breadcrumbs[0])?$this->breadcrumbs[0]:"") == "Update sensor"
		|| (isset($this->breadcrumbs[0])?$this->breadcrumbs[0]:"") == "Update user"):?>
	<div id="page">
	    <div class="bgtop"></div>
	    <div class="content-bg">
		<div id ="content" >
		    <div class="border" >
		    <?php if (isset($this->breadcrumbs)): ?>
			<?php
			    $this->widget('zii.widgets.CBreadcrumbs', array(
			    'links' => $this->breadcrumbs,
			    ));
			?><!-- breadcrumbs -->
		    <?php endif ?>
		</div>

		    <?php echo $content; ?>
		</div>
	    </div>
	</div>
		<?php else: ?>
	<div id="page">
	    <div class="content-bg">
		<div id ="content">
		    <div class="border">
		    <?php if (isset($this->breadcrumbs)): ?>
			<?php
			    $this->widget('zii.widgets.CBreadcrumbs', array(
			    'links' => $this->breadcrumbs,
			    ));
			?><!-- breadcrumbs -->
		    <?php endif ?>
		</div>

		    <?php echo $content; ?>
		</div>
	    </div>
		<?php endif; ?>
	</div>
	<div id="footer-content">
	    <div class="bgtop"></div>
	    <div class="content-bg">
		<div id="column1">
		    <div class="box1">
			<h2>What is GSN?</h2>
			<p><a href ="http://sourceforge.net/apps/trac/gsn/">GSN</a> stands for Global sensor networks project. Basically it is a web server that collects various data from multiple sources.</p>
		    </div>
		</div>
	    </div>
	    <div class="bgbtm"></div>
	</div>
        <div id="footer">
            <p>Copyright &copy; <?php echo date('Y'); ?> by FER, University of Zagreb.</p>
            <p>All Rights Reserved.</p>
        </div><!-- footer -->
    </body>
</html>
