<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="language" content="en" />

        <!-- blueprint CSS framework -->
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
        <!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />

        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    </head>

    <body>

        <div class="container" id="page">

            <div id="header">
                <img src="<?php echo Yii::app()->request->baseUrl; ?>/css/pictures/lion-banner.jpg" alt="lion-head" height="150" width="950"/>
                <div id="user_name"><?php if (!Yii::app()->user->isGuest)
            echo "Welcome " . Yii::app()->user->name; ?></div>
            </div><!-- header -->

            <div id="mainmenu">
                <?php
                $this->widget('zii.widgets.CMenu', array(
                    'items' => array(
                        array('label' => 'Home', 'url' => array('/site/index')),
                        array('label' => 'GSN', 'url' => array('/user/userGsnList'), 'visible' => !Yii::app()->user->isGuest),
                        array('label' => 'Sensors', 'url' => array('/user/userSensors'), 'visible' => !Yii::app()->user->isGuest),
                        array('label' => 'Graph-Josip', 'url' => array('/graphs/graphs'), 'visible' => !Yii::app()->user->isGuest),
                        array('label' => 'Reports', 'url' => array('/reportSubscription/userReportsMain'), 'visible' => !Yii::app()->user->isGuest),
                        array('label' => 'Graphs', 'url' => array('/user/userGraphViewer'), 'visible' => !Yii::app()->user->isGuest),
                        array('label' => 'Contact', 'url' => array('/site/contact')),
                        array('label' => 'Watchdog timers', 'url' => array('/user/userWatchdogTimer'), 'visible' => !Yii::app()->user->isGuest),
                        array('label' => 'Notifications', 'url' => array('/user/userNotifications'), 'visible' => !Yii::app()->user->isGuest),
                        array('label' => 'Login', 'url' => array('/site/login'), 'visible' => Yii::app()->user->isGuest),
                        array('label' => 'Logout (' . Yii::app()->user->name . ')', 'url' => array('/site/logout'), 'visible' => !Yii::app()->user->isGuest),
                        array('label' => 'Admin Panel', 'url' => array('/admin/index'), 'visible' => !Yii::app()->user->isGuest && Yii::app()->user->group == 1),
                    ),
                ));
                ?>
            </div><!-- mainmenu -->

            <!--Side menu for watchdog timers tab-->
            <div id="sidemenu">
                <?php
                $this->widget('zii.widgets.CMenu', array(
                    'itemCssClass' => 'nav',
                    'items' => array(
                        array('label' => 'New', 'url' => array('/user/userWatchdogTimer')),
                        array('label' => 'SMS watchdog timer', 'url' => array('/user/userSmsWatchdogRequests')),
                        array('label' => 'E-mail watchdog timer', 'url' => array('/user/userEmailWatchdogRequests')),
                    ),
                ));
                ?>
            </div><!-- sidemenu -->
            <div id="content">
                <?php if (isset($this->breadcrumbs)): ?>
                <?php
                    $this->widget('zii.widgets.CBreadcrumbs', array(
                        'links' => $this->breadcrumbs,
                    ));
                ?><!-- breadcrumbs -->
                <?php endif ?>

                <?php echo $content; ?>

                    <div class="clear"></div>

                    <div id="footer">
        		Copyright &copy; <?php echo date('Y'); ?> by FER.<br/>
		All Rights Reserved.<br/>
                    Matija Renić, Luka Postružin<br/>
                </div><!-- footer -->
            </div>
        </div><!-- page -->

    </body>
</html>
