<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="language" content="en" />
        <!-- <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/tableDesign.css" /> -->
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
        <link href="http://fonts.googleapis.com/css?family=Oswald:400,700" rel="stylesheet" type="text/css" />
		<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all" />
		<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css" media="all" />
		<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/default_after_login.css" rel="stylesheet" type="text/css" media="all" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/incrementButton.css" />

<!--        <script type="text/javascript" src="<?php //echo Yii::app()->request->baseUrl; ?>/protected/javascript/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="<?php //echo Yii::app()->request->baseUrl; ?>/protected/javascript/jquery.dropotron-1.0.js"></script>
        <script type="text/javascript" src="<?php //echo Yii::app()->request->baseUrl; ?>/protected/javascript/init.js"></script>-->
	<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js'></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/protected/javascripts/incrementing.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/protected/javascripts/refreshing_table.js"></script>
        <script language="JavaScript">
            function reload(form){
                var not_type="not_selected";

                var radioButtons = document.getElementsByName('UserNotifications[notification_type]');

                not_type = radioButtons.length;

                for (var x = 0; x < radioButtons.length; x ++) {

                    if (radioButtons[x].type=='radio' && radioButtons[x].checked) {
                        not_type=radioButtons[x].value;
                    }
                }

                self.location = '/lion/index.php/user/userNotifications?notification_type=' + not_type;
            }
	    function reloadWatchdog(form){
                var not_type="not_selected";

                var radioButtons = document.getElementsByName('UserWatchdogTimer[watchdog_type]');

                not_type = radioButtons.length;

                for (var x = 0; x < radioButtons.length; x ++) {

                    if (radioButtons[x].type=='radio' && radioButtons[x].checked) {
                        not_type=radioButtons[x].value;
                    }
                }

                self.location = '/lion/index.php/user/userWatchdogTimer?watchdog_type=' + not_type;
            }
        </script>
	<script type="text/javascript">
	    var auto_refresh = setInterval(
	    function ()
	    {
	    $('#managing').load(<?php echo '\'http://161.53.67.224'.Yii::app()->createUrl('systemManaging/managingLiveStream').'\'' ?>).fadeIn("slow");
	    }, 3000); // refresh every 10000 milliseconds

	    $(function() {
		if($("#user_notifications_notification_type_0").is(":checked")){
		    $('#phone').hide();
		    $('#email').hide();
		}else{
		    $('#email').hide();
		    $('#phone').hide();
		}

		$("[name='UserNotifications[notification_type]']").change(function(){
		    if($(this).val()=="1"){
			$('#phone').show();
			$('#email').hide();
		    }else {
			$('#email').show();
			$('#phone').hide();
		    }

		})
	    });
	</script>
	<script type="text/javascript">
	    var auto_refresh = setInterval(

	    function ()
	    {
	    $('#managing').load(<?php echo '\'http://161.53.67.224'.Yii::app()->createUrl('systemManaging/managingLiveStream').'\'' ?>).fadeIn("slow");
	    }, 3000);



	    $(function() {
		if($("#user_watchdog_timer_watchdog_type_0").is(":checked")){
		    $('#phone').hide();
		    $('#email').hide();
		}else{
		    $('#email').hide();
		    $('#phone').hide();
		}

		$("[name='UserWatchdogTimer[watchdog_type]']").change(function(){
		    if($(this).val()=="1"){
			$('#phone').show();
			$('#email').hide();
		    }else {
			$('#email').show();
			$('#phone').hide();
		    }

		})
	    });
	</script>












	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
    </head>
    <body>
	<?php if (!Yii::app()->user->isGuest) :?>
	<!--
	<div id="center250b">
	<div id="fixedtop2">
	-->
	<div id="header-wrapper">
	    <div id="header">
		<div id="logo">
		    <h1><a href="#">Lion</a></h1>
		    <p><?php if (!Yii::app()->user->isGuest) echo "Welcome " . Yii::app()->user->name; ?></p>
		    <div id="menu-wrapper">
		<ul id="menu">
		    <?php if (Yii::app()->user->isGuest) : ?>
			<li <?php if ($this->breadcrumbs[0] == "Home") echo 'class="first"' ?>><?php echo CHtml::link('<span>Home</span>', array('/site/index')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Contact") echo 'class="first"' ?>><?php echo CHtml::link('<span>Contact</span>', array('/site/contact')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Login") echo 'class="first"' ?>><?php echo CHtml::link('<span>Login</span>', array('/site/login')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Registration") echo 'class="first"' ?>><?php echo CHtml::link('<span>Registration</span>', array('/site/registrationForm')); ?></li>
		    <?php else : ?>
			<li <?php if ($this->breadcrumbs[0] == "Personal") echo 'class="first"' ?>><?php echo CHtml::link('<span>Personal</span>', array('/user/userPersonal')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "GSN") echo 'class="first"' ?>><?php echo CHtml::link('<span>GSN</span>', array('/user/userGsnList')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Sensors") echo 'class="first"' ?>><?php echo CHtml::link('<span>Sensors</span>', array('/user/userSensors')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Notification" || $this->breadcrumbs[0]=='Notification requests') echo 'class="first"' ?>><?php echo CHtml::link('<span>Notifications</span>', array('/user/userNotifications')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "GSN managing") echo 'class="first"' ?>><?php echo CHtml::link('<span>System</span>', array('/systemManaging/heatingControl')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Watchdog timers") echo 'class="first"' ?>><?php echo CHtml::link('<span>Watchdog</span>', array('/user/userWatchdogTimer')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Reports" || $this->breadcrumbs[0] =="New report subscription" || $this->breadcrumbs[0] =="Report subscriptions" || $this->breadcrumbs[0] =="Daily reports" || $this->breadcrumbs[0] =="Monthly reports") echo 'class="first"' ?>><?php echo CHtml::link('<span>Reports</span>', array('/reportSubscription/userReportsMain')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Graphs") echo 'class="first"' ?>><?php echo CHtml::link('<span>Graphs</span>', array('/graphs/graphs')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Contact")echo 'class="first"' ?>><?php echo CHtml::link('<span>Contact</span>', array('/user/contact')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Logout") echo 'class="first"' ?>><?php echo CHtml::link('<span>Logout</span>', array('/site/logout')); ?></li>
			<?php if (Yii::app()->user->group == 1) : ?>
			    <li><?php echo CHtml::link('<span>Admin</span>', array('/admin/index')); ?></li>
			<?php endif; ?>
		    <?php endif; ?>
		</ul>
		    </div>
		    		</div>
	    </div>
	    </div>
	<!--
	</div>
	</div>
	-->
	<div id="page_main" style="margin-top:20px">
	<div id="page">
	    <div class="bgtop"></div>
	    <div class="content-bg">
		<div id="content">
		    <?php echo $content; ?>
		</div>

	    <?php if ($this->breadcrumbs[0]=='Notification' || $this->breadcrumbs[0]=='Notification requests') : ?>
	    <div id="sidebar">
		<div>
		    <h3 class="title">Options</h3>
		    <p>
			<ul class="nav nav-pills">
			    <li <?php if ($this->breadcrumbs[0] == "Notification") echo 'class="first"' ?>><?php echo CHtml::link('<span>New notification</span>', array('/user/userNotifications')); ?></li>
			    <li <?php if ($this->breadcrumbs[0] == "Notification requests") echo 'class="first"' ?>><?php echo CHtml::link('<span>Notification requests</span>', array('/user/userNotificationRequests')); ?></li>
			</ul>
		    </p>
		</div>
	    </div>
	  <?php elseif ($this->breadcrumbs[0]=='Watchdog timers' || $this->breadcrumbs[0]=='Watchdog timer requests') : ?>
	    <div id="sidebar">
		<div>
		    <h3 class="title">Options</h3>
		    <p>
			<ul class="nav nav-pills">
			    <li <?php if ($this->breadcrumbs[0] == "Watchdog timer") echo 'class="first"' ?>><?php echo CHtml::link('<span>New watchdog</span>', array('/user/userWatchdogTimer')); ?></li>
			    <li <?php if ($this->breadcrumbs[0] == "Watchdog timer requests") echo 'class="first"' ?>><?php echo CHtml::link('<span>Watchdog requests</span>', array('/user/userWatchdogRequests')); ?></li>
			</ul>
		    </p>
		</div>
	    </div>
	    <?php elseif ($this->breadcrumbs[0]=='New report subscription' || $this->breadcrumbs[0]=='Report subscriptions' || $this->breadcrumbs[0]=='Monthly reports' || $this->breadcrumbs[0]=='Daily reports' || $this->breadcrumbs[0]=='Reports') :?>
	    <div id="sidebar">
		<div>
		    <h3 class="title">Options</h3>
		    <p>
		    <ul class="nav nav-pills">

			<li <?php if ($this->breadcrumbs[0] == "Reports") echo 'class="first"' ?>><?php echo CHtml::link('<span>Reports</span>', array('/reportSubscription/userReportsMain')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Daily reports") echo 'class="first"' ?>><?php echo CHtml::link('<span>Daily reports</span>', array('/reportSubscription/userReportsDaily')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Monthly reports") echo 'class="first"' ?>><?php echo CHtml::link('<span>Monthly reports</span>', array('/reportSubscription/userReportsMonthly')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "New report subscription") echo 'class="first"' ?>><?php echo CHtml::link('<span>New report subscription</span>', array('/reportSubscription/userReportsNewSubscription')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Report subscriptions") echo 'class="first"' ?>><?php echo CHtml::link('<span>Report subscriptions</span>', array('/reportSubscription/userReportsSubscription')); ?></li>
		    </ul>
		    </p>
		</div>

	    </div>
		<?php else: ?>
	    <div id="sidebar">

	    </div>
	    <?php endif;?>
	</div>
	<div class="bgbtm"></div>
	</div>
	<div id="footer-content">
	    <div class="bgtop"></div>
	    <div class="content-bg">
		<div id="column1">
		    <div class="box1">
		    <h2>Want a new report?</h2>
		    You can review reports online or request one to be sent on your private email.
		    <ul>
			<li <?php if ($this->breadcrumbs[0] == "Reports") echo 'class="first"' ?>><?php echo CHtml::link('<span>Reports</span>', array('/reportSubscription/userReportsMain')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Daily reports") echo 'class="first"' ?>><?php echo CHtml::link('<span>Daily reports</span>', array('/reportSubscription/userReportsDaily')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Monthly reports") echo 'class="first"' ?>><?php echo CHtml::link('<span>Monthly reports</span>', array('/reportSubscription/userReportsMonthly')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "New report subscription") echo 'class="first"' ?>><?php echo CHtml::link('<span>New report subscription</span>', array('/reportSubscription/userReportsNewSubscription')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Report subscriptions") echo 'class="first"' ?>><?php echo CHtml::link('<span>Report subscriptions</span>', array('/reportSubscription/userReportsSubscription')); ?></li>
		    </ul>
		    </div>
		    <div class="box2">
		    <h2>Want to be notified?</h2>
		    Don't have time to look at the webpage all the time, let us notify you when neccessary!<br/><br/>
		    <ul>
			<li <?php if ($this->breadcrumbs[0] == "Notification") echo 'class="first"' ?>><?php echo CHtml::link('<span>New notification</span>', array('/user/userNotifications')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Notification requests") echo 'class="first"' ?>><?php echo CHtml::link('<span>Notification requests</span>', array('/user/userNotificationRequests')); ?></li>
		    </ul>
		    </div>
		</div>
	    </div>
	    <div class="bgbtm"></div>
	</div>
        <div id="footer">
            <p>Copyright &copy; <?php echo date('Y'); ?> by FER, University of Zagreb.</p>
            <p>All Rights Reserved.</p>
        </div><!-- footer -->
	</div>

	<?php else: ?>
	<div id="center250b">
	<div id="fixedtop2">
	<div id="header-wrapper">
	    <div id="header">
		<div id="logo">
		    <h1><a href="#">Lion</a></h1>
		    <p><?php if (!Yii::app()->user->isGuest) echo "Welcome " . Yii::app()->user->name; ?></p>

		<ul id="menu">
			<li <?php if ($this->breadcrumbs[0] == "Home") echo 'class="first"' ?>><?php echo CHtml::link('<span>Home</span>', array('/site/index')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Contact") echo 'class="first"' ?>><?php echo CHtml::link('<span>Contact</span>', array('/site/contact')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Login") echo 'class="first"' ?>><?php echo CHtml::link('<span>Login</span>', array('/site/login')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Registration") echo 'class="first"' ?>><?php echo CHtml::link('<span>Registration</span>', array('/site/registrationForm')); ?></li>

		</ul>
		</div>
	    </div>
	    </div>
	</div>

	</div>
	<div id="page_main">
	<div id="page">
	    <div class="bgtop"></div>
	    <div class="content-bg">
		<div id="content">
		    <?php echo "You need to log in before viewing this page! It seems your log in information was invalid!"; ?>
		</div>
	    </div>
	</div>

        <div id="footer">
            <p>Copyright &copy; <?php echo date('Y'); ?> by FER, University of Zagreb.</p>
            <p>All Rights Reserved.</p>
        </div><!-- footer -->
	</div>
	<?php endif; ?>
    </body>
</html>