<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class UserController extends Controller {

    public $layout = 'main_after_login';

    /**
     * Declares class-based actions.
     */
    public function actions() {
	return array(
	    // captcha action renders the CAPTCHA image displayed on the contact page
	    'captcha' => array(
		'class' => 'CCaptchaAction',
		'backColor' => 0xFFFFFF,
	    ),
	    // page action renders "static" pages stored under 'protected/views/site/pages'
	    // They can be accessed via: index.php?r=site/page&view=FileName
	    'page' => array(
		'class' => 'CViewAction',
	    ),
	);
    }

    /*
     * Personal information for our user
     */

    public function actionUserPersonal() {
	$model = new ProdUsers();
	$model = ProdUsers::model()->findByPk(Yii::app()->user->id);

	if (isset($_POST['ProdUsers'])) {
	    $model->attributes = $_POST['ProdUsers'];
	    //$this->render('userPersonal', array('model' => $model));

	    if ($model->save())
		$this->render('userPersonal', array('model' => $model, 'message' => "Your changes have been saved!<br/>"));
	    else
		$this->render('userPersonal', array('model' => $model, 'message' => "Problem occured during administration. Please try again!<br/>"));
	}
	else
	    $this->render('userPersonal', array('model' => $model));
    }

    /**
     * Displays the contact page
     */
    public function actionContact() {
	$model = new ContactForm;
	$model->message = "<b>If you have business inquiries or other questions, please fill out the following form to contact us. Thank you in advance.</b>";

	if (isset($_POST['ContactForm'])) {
	    $model->attributes = $_POST['ContactForm'];
	    if ($model->validate()) {
		$headers = "From: {$model->email}\r\nReply-To: {$model->email}";
		mail(Yii::app()->params['adminEmail'], $model->subject, $model->body, $headers);
		//Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
		$model->message = "<b>Your message has successfully been sent! Thank you for your interest, we will try to repond as soon as possible.<b>";
		$model->body = "Do you have some more questions?";
		$model->subject = "";
		$this->render('contact', array('model' => $model));
	    }
	}
	$this->render('contact', array('model' => $model));
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
	if ($error = Yii::app()->errorHandler->error) {
	    if (Yii::app()->request->isAjaxRequest)
		echo $error['message'];
	    else
		$this->render('error', $error);
	}
    }

    /**
     * Actions for the notification dropdown menus
     */
    public function actionNotificationsDynamicSensors() {
	$data = DiSensors::model()->findAll('gsn_id=:parent_id',
			array(':parent_id' => (int) $_POST['UserNotifications']['gsn_list']));


	$data = CHtml::listData($data, 'sensor_id', 'sensor_user_name');
	echo CHtml::tag('option', array('value' => 0), 'Select', true);
	foreach ($data as $id => $value) {
	    echo CHtml::tag('option', array('value' => $id), CHtml::encode($value), true);
	}
    }

    public function actionNotificationsDynamicUnits() {
	$data = DiUnits::model()->with(array(
		    'fSensorTypes' => array(
			'select' => false,
			'joinType' => 'INNER JOIN',
		    ),
		))->findAll('"fSensorTypes".sensor_id=:parent_id',
			array(':parent_id' => (int) $_POST['UserNotifications']['sensor_list']));

	$data = CHtml::listData($data, 'unit_id', 'unit_name');
	echo CHtml::tag('option', array('value' => 0), 'Select', true);
	foreach ($data as $id => $value) {
	    echo CHtml::tag('option', array('value' => $id), CHtml::encode($value), true);
	}
    }

    /**
     * Actions for the notification dropdown menus
     */
    public function actionWatchdogDynamicSensors() {
	$data = DiSensors::model()->findAll('gsn_id=:parent_id',
			array(':parent_id' => (int) $_POST['UserWatchdogTimer']['gsn_list']));


	$data = CHtml::listData($data, 'sensor_id', 'sensor_user_name');
	echo CHtml::tag('option', array('value' => 0), 'Select', true);
	foreach ($data as $id => $value) {
	    echo CHtml::tag('option', array('value' => $id), CHtml::encode($value), true);
	}
    }

    /**
     * Displays the notifications page
     */
    public function actionUserNotifications() {
	$model = new UserNotifications;

	$model->notification_type = ((int) !empty($_POST['UserNotifications']['notification_type']) ? $_POST['UserNotifications']['notification_type'] : ((int) !empty($_GET['notification_type']) ? $_GET['notification_type'] : 99999));


	$model->gsn_id = ((int) !empty($_POST['UserNotifications']['gsn_list']) ? $_POST['UserNotifications']['gsn_list'] : ((int) !empty($_GET['gsn_id']) ? $_GET['gsn_id'] : 99999));
	$model->sensor_id = ((int) !empty($_POST['UserNotifications']['sensor_list']) ? $_POST['UserNotifications']['sensor_list'] : ((int) !empty($_GET['sensor_id']) ? $_GET['sensor_id'] : 99999));
	$model->unit_id = ((int) !empty($_POST['UserNotifications']['measuring_unit_list']) ? $_POST['UserNotifications']['measuring_unit_list'] : ((int) !empty($_GET['unit_id']) ? $_GET['unit_id'] : 99999));
	$model->critical_value = ((int) !empty($_POST['UserNotifications']['critical_value']) ? $_POST['UserNotifications']['critical_value'] : ((int) !empty($_GET['critical_value']) ? $_GET['critical_value'] : null));
	$model->resending_interval = ((int) !empty($_POST['UserNotifications']['resending_interval']) ? $_POST['UserNotifications']['resending_interval'] : ((int) !empty($_GET['resending_interval']) ? $_GET['resending_interval'] : null));
	$model->criteria_type = ((int) !empty($_POST['UserNotifications']['criteria_type']) ? $_POST['UserNotifications']['criteria_type'] : ((int) !empty($_GET['criteria_type']) ? $_GET['criteria_type'] : 99999));
	if ($model->criteria_type == 0)
	    $model->criteria_type_name = "above";
	else if ($model->criteria_type == 1)
	    $model->criteria_type_name = "below";
	$model->email = ((string) !empty($_POST['UserNotifications']['email']) ? $_POST['UserNotifications']['email'] : ((int) !empty($_GET['email']) ? $_GET['email'] : null));
	$model->phone_number = ((string) !empty($_POST['UserNotifications']['phone_number']) ? $_POST['UserNotifications']['phone_number'] : ((int) !empty($_GET['phone_number']) ? $_GET['phone_number'] : null));

	//$date_not = new CDbExpression('NOW()');

	if (isset($_POST['UserNotifications'])) {
	    $model->attributes = $_POST['UserNotifications'];

	    if ($model->notification_type == 1) { //we are dealing with sms notification
		$model_notifications = new ProdSmsNotifications;
		//$model_notification->time_notification_asked = new CDbExpression('NOW()');
		$model_notifications->unit_id = $model->unit_id;
		$unit_row = DiUnits::model()->find(array(
			    'select' => 'unit_name',
			    'condition' => 'unit_id = ' . $model->unit_id));
		$model_notifications->unit_name = (string) $unit_row['unit_name'];
		$model_notifications->unit_name_upper = strtoupper($model_notifications->unit_name);
		$model_notifications->sensor_id = $model->sensor_id;

		$sensor_row = DiSensors::model()->find(array(
			    'select' => 'sensor_user_name, gsn_id',
			    'condition' => 'sensor_id = ' . $model->sensor_id));
		$model_notifications->sensor_name = (string) $sensor_row['sensor_user_name'];
		$model_notifications->gsn_id = $sensor_row['gsn_id'];
		$model_notifications->prod_user_id = Yii::app()->user->id;
		$model_notifications->critical_value = $model->critical_value;
		$model_notifications->resending_interval = $model->resending_interval;
		$model_notifications->phone = $model->phone_number;
		$model_notifications->criteria_type = $model->criteria_type;
		$model_notifications->criteria_type_name = $model->criteria_type_name;

		if ($model_notifications->criteria_type == 0)
		    $model_notifications->xml_name = "N_" . Yii::app()->user->username . "_" . $model_notifications->sensor_name . "3_" . strtotime(date('m/d/Y h:i:s a', time()));
		else
		    $model_notifications->xml_name = "N_" . Yii::app()->user->username . "_" . $model_notifications->sensor_name . "4_" . strtotime(date('m/d/Y h:i:s a', time()));

		//$model_notifications->is_active = '0';
		$body = "prod_user_id = " . $model_notifications->prod_user_id .
			"\nUsername = " . Yii::app()->user->username .
			"\nSensor_id = " . $model_notifications->sensor_id .
			"\nUnit id = " . $model_notifications->unit_id .
			"\nUnit name = " . $model_notifications->unit_name .
			"\nUnit name upper = " . $model_notifications->unit_name_upper .
			"\nCritical value = " . $model_notifications->critical_value .
			"\nResending Interval = " . $model_notifications->resending_interval .
			"\nCriteria type = " . $model_notifications->criteria_type .
			"\nNotification type = " . $model->notification_type .
			"\nPhone = " . $model_notifications->phone
		;
	    } else if ($model->notification_type == 2) { //we are dealing with email notification
		$model_notifications = new ProdEmailNotifications;
		//$model_notification->time_notification_asked = new CDbExpression('NOW()');
		$model_notifications->unit_id = $model->unit_id;
		$unit_row = DiUnits::model()->find(array(
			    'select' => 'unit_name',
			    'condition' => 'unit_id = ' . $model->unit_id));
		$model_notifications->unit_name = (string) $unit_row['unit_name'];
		$model_notifications->unit_name_upper = strtoupper($model_notifications->unit_name);
		$model_notifications->sensor_id = $model->sensor_id;
		$sensor_row = DiSensors::model()->find(array(
			    'select' => 'sensor_user_name,gsn_id',
			    'condition' => 'sensor_id = ' . $model->sensor_id));
		$model_notifications->sensor_name = (string) $sensor_row['sensor_user_name'];
		$model_notifications->gsn_id = $sensor_row['gsn_id'];
		//$model_notifications->gsn_id = $model->gsn_id;
		$model_notifications->prod_user_id = Yii::app()->user->id;
		$model_notifications->critical_value = $model->critical_value;
		$model_notifications->resending_interval = $model->resending_interval;
		$model_notifications->email = $model->email;
		$model_notifications->criteria_type = $model->criteria_type;
		$model_notifications->criteria_type_name = $model->criteria_type_name;

		if ($model_notifications->criteria_type == 0)
		    $model_notifications->xml_name = "N_" . Yii::app()->user->username . "_" . $model_notifications->sensor_name . "1_" . strtotime(date('m/d/Y h:i:s a', time()));
		else
		    $model_notifications->xml_name = "N_" . Yii::app()->user->username . "_" . $model_notifications->sensor_name . "2_" . strtotime(date('m/d/Y h:i:s a', time()));

		//$model_notifications->is_active = '0';
		$body = "prod_user_id = " . $model_notifications->prod_user_id .
			"\nUsername = " . Yii::app()->user->username .
			"\nSensor_id = " . $model_notifications->sensor_id .
			"\nUnit id = " . $model_notifications->unit_id .
			"\nUnit name = " . $model_notifications->unit_name .
			"\nUnit name upper = " . $model_notifications->unit_name_upper .
			"\nCritical value = " . $model_notifications->critical_value .
			"\nResending Interval = " . $model_notifications->resending_interval .
			"\nCriteria type = " . $model_notifications->criteria_type .
			"\nNotification type = " . $model->notification_type .
			"\nEmail = " . $model->email
		;
	    }
	    else
		$body = "Nema tog notification Type-a";

	    $headers = "From: " . Yii::app()->params['adminEmail'] . "\r\nReply-To: " . Yii::app()->params['adminEmail'];
	    $subject = "Notification info";
	    //$body = "Login username : ".$this->username."!\r\n\nLogin password : ".$this->password;
	    //mail("hyracoidea@gmail.com", $subject, $body, $headers);

	    if ($model->validate()) {
		if ($model_notifications->save()) {
		    $headers = "From: " . Yii::app()->params['adminEmail'] . "\r\nReply-To: " . Yii::app()->params['adminEmail'];
		    $subject = "Notification info";
		    $body.="\nNotification successfully saved with given parameters";
		    //$body = "Login username : ".$this->username."!\r\n\nLogin password : ".$this->password;
		    try {
			mail("hyracoidea@gmail.com", $subject, $body, $headers);
		    } catch (Exception $ae) {
			echo "Problem occured while sending email for notification saving!";
		    }
		} else {
		    $headers = "From: " . Yii::app()->params['adminEmail'] . "\r\nReply-To: " . Yii::app()->params['adminEmail'];
		    $subject = "Notification info";
		    //$body = "Login username : ".$this->username."!\r\n\nLogin password : ".$this->password;
		    try {
			mail("hyracoidea@gmail.com", $subject, "Neuspjesno spremanje zahtjeva", $headers);
		    } catch (Exception $ae) {
			echo "Problem occured while sending email for notification saving!";
		    }
		}
		//Yii::app()->user->setFlash('notifications','Thank you for your time to fill in the form. Your request has been activated.');
		$this->refresh();
	    }
	}
	$this->render('userNotifications', array('model' => $model));
    }

    /**
     * Displays the gsnList page
     */
    public function actionUserGsnList() {
	$model = new UserGsnList;

	//if user has chosen any gsn we will find this field not empty. This means that we need to provide new gsn_privilege request if there is
	//not already this request in the database
	if (isset($_REQUEST['gsn_id'])) {
	    $model_privilege = new DiGsnPrivileges;
	    //if we find a row that equals this one, we do nothing at all
	    if ((DiGsnPrivileges::model()->findByAttributes(array('gsn_id' => $_REQUEST['gsn_id'], 'user_id' => Yii::app()->user->id))) == null) {
		$model_privilege->gsn_id = $_REQUEST['gsn_id'];
		$model_privilege->user_id = Yii::app()->user->id;
		//at first, this request is not active
		$model_privilege->is_active = 0;
		$model_privilege->date_id_given = 19000101;
		if ($model_privilege->save())
		    $this->redirect(array('user/userGsnList'));
	    }
	    else {
		DiGsnPrivileges::model()->deleteAllByAttributes(array('gsn_id' => $_REQUEST['gsn_id'], 'user_id' => Yii::app()->user->id));
		$this->redirect(array('user/userGsnList'));
	    }
	}

	$this->render('userGsnList', array('model' => $model));
    }

    /**
     * Email notification requests - user managing
     */
    public function actionUserNotificationRequests() {
	//if administrator has chosen any notification request we need to provide this request (approve, decline)
	if (isset($_REQUEST['notification_id']) && (isset($_REQUEST['type']))) {
	    if ($_REQUEST['type'] != "SMS") {
		$headers = "From: " . Yii::app()->params['adminEmail'] . "\r\nReply-To: " . Yii::app()->params['adminEmail'];
		$subject = "Email Notification approval/disapproval";
		$body = "";

		$start = "Notification request deleting process started! Time: " . date('Y-m-d H:i:s');

		$email_notification = new ProdEmailNotifications;

		$email_notification = ProdEmailNotifications::model()->findByAttributes(array('notification_id' => $_REQUEST['notification_id']));
		//if we find a row that equals this one, we do nothing at all

		if (($email_notification) != null) { //if notification request exists proceed
		    //we first collect info on GSN server of this notification
		    $gsn_row = DiGsn::model()->find(array('select' => 'gsn_name, gsn_ip, notification_folder, notification_backup_folder, sftp_username, sftp_password', 'condition' => 'gsn_id=' . $email_notification->gsn_id));

		    if ($gsn_row != null) {
			try {
			    if (strcmp($gsn_row['gsn_ip'], "127.0.0.1") == 0) {
				$file_w = fopen($gsn_row['notification_folder'] . "\\" . $email_notification->xml_name . ".xml", 'w');
				delete($file_w);

				$file_w_backup = fopen($gsn_row['notification_backup_folder'] . "\\" . $email_notification->xml_name . ".xml", 'w');
				delete($file_w_backup);
			    } else {
				$sftp_obj = new SftpComponent($gsn_row['gsn_ip'], $gsn_row['sftp_username'], $gsn_row['sftp_password']);
				$sftp_obj->connect();

				try {
				    //first we try to delete original notification file
				    if ($sftp_obj->isDir($gsn_row['notification_folder']))
					$body.="\nNotification backup folder exists!\n";
				    else
					$body.="\nNotification backup folder does not exist!\n";
				    $sftp_obj->chdir($gsn_row['notification_folder']);
				    $sftp_obj->removeFile($email_notification->xml_name . ".xml");
				} catch (Exception $er) {
				    $body = $body . "Primary notification was not succesfully removed. Please check the problem!\nError message " . $er->getMessage() . "\n";
				}

				try {
				    //then we try to delete backup notification file
				    if ($sftp_obj->isDir($gsn_row['notification_backup_folder']))
					$body.="\nNotification backup folder exists!\n";
				    else
					$body.="\nNotification backup folder does not exist!\n";

				    $sftp_obj->chdir($gsn_row['notification_backup_folder']);
				    $sftp_obj->removeFile($email_notification->xml_name . ".xml");
				} catch (Exception $er) {
				    $body = $body . "Backup notification was not succesfully removed. Please check the problem!\nError message " . $er->getMessage() . "\n";
				}
				//if this was successful we need to inform our administrator about the action
				if ($body == "")
				    $body = "File was succesfully removed!\nGSN name: " . $gsn_row['gsn_name'] . "Notification ID: " . $email_notification->nofitication_id;
			    }
			} catch (Exception $e) {
			    $body = "File was NOT successfully removed! Please make a manual check on the problem!\nIt seems that the connection could not be established.\nNotification ID: " . $email_notification->notification_id . "\nError message: " . $e->getMessage();
			}

			//mail("hyracoidea@gmail.com", $subject, $start . "\n" . $body . "Notification request deleting process finished! Time: " . date('Y-m-d H:i:s'), $headers);

			if ($email_notification->delete())
			    $body.="\nNotification successfuly deleted!";
			else
			    $body.="\nNotification could not be deleted!";
		    }
		    else
			$body = "Something went wrong when acquiring data for the GSN!\nNotification ID: " . $email_notification->notification_id;
		}
		else
		    $body = "There was no email notification request with Notification ID: " . $_REQUEST['notification_id'];

		try {
		    mail("hyracoidea@gmail.com", $subject, $start . "\n" . $body . "Notification request deleting process finished! Time: " . date('Y-m-d H:i:s'), $headers);
		} catch (Exception $ae) {
		    echo "Problem occured with mail sending!";
		}
	    } else {
		$headers = "From: " . Yii::app()->params['adminEmail'] . "\r\nReply-To: " . Yii::app()->params['adminEmail'];
		$subject = "Email Notification approval/disapproval";
		$body = "";
		//if administrator has chosen any notification request we need to provide this request (approve, decline)
		$start = "Notification request deleting process started! Time: " . date('Y-m-d H:i:s');

		$email_notification = new ProdEmailNotifications;

		$sms_notification = ProdSmsNotifications::model()->findByAttributes(array('notification_id' => $_REQUEST['notification_id']));
		//if we find a row that equals this one, we do nothing at all

		if (($sms_notification) != null) { //if notification request exists proceed
		    //we first collect info on GSN server of this notification
		    $gsn_row = DiGsn::model()->find(array('select' => 'gsn_name, gsn_ip, notification_folder, notification_backup_folder, sftp_username, sftp_password', 'condition' => 'gsn_id=' . $email_notification->gsn_id));

		    if ($gsn_row != null) {
			try {

			    if (strcmp($gsn_row['gsn_ip'], "127.0.0.1") == 0) {
				$file_w = fopen($gsn_row['notification_folder'] . "\\" . $email_notification->xml_name . ".xml", 'w');
				delete($file_w);

				$file_w_backup = fopen($gsn_row['notification_backup_folder'] . "\\" . $email_notification->xml_name . ".xml", 'w');
				delete($file_w_backup);
			    } else {
				$sftp_obj = new SftpComponent($gsn_row['gsn_ip'], $gsn_row['sftp_username'], $gsn_row['sftp_password']);
				$sftp_obj->connect();

				try {
				    //first we try to delete original notification file
				    if ($sftp_obj->isDir($gsn_row['notification_folder']))
					$body.="\nNotification backup folder exists!\n";
				    else
					$body.="\nNotification backup folder does not exist!\n";
				    $sftp_obj->chdir($gsn_row['notification_folder']);
				    $sftp_obj->removeFile($sms_notification->xml_name . ".xml");
				} catch (Exception $er) {
				    $body = $body . "Primary notification was not succesfully removed. Please check the problem!\nError message " . $er->getMessage() . "\n";
				}

				try {
				    //then we try to delete backup notification file
				    if ($sftp_obj->isDir($gsn_row['notification_backup_folder']))
					$body.="\nNotification backup folder exists!\n";
				    else
					$body.="\nNotification backup folder does not exist!\n";

				    $sftp_obj->chdir($gsn_row['notification_backup_folder']);
				    $sftp_obj->removeFile($sms_notification->xml_name . ".xml");
				} catch (Exception $er) {
				    $body = $body . "Backup notification was not succesfully removed. Please check the problem!\nError message " . $er->getMessage() . "\n";
				}
				//if this was successful we need to inform our administrator about the action
				if ($body == "")
				    $body = "File was succesfully removed!\nGSN name: " . $gsn_row['gsn_name'] . "Notification ID: " . $sms_notification->nofitication_id;
			    }
			} catch (Exception $e) {
			    $body = "File was NOT successfully removed! Please make a manual check on the problem!\nIt seems that the connection could not be established.\nNotification ID: " . $email_notification->notification_id . "\nError message: " . $e->getMessage();
			}

			//mail("hyracoidea@gmail.com", $subject, $start . "\n" . $body . "Notification request deleting process finished! Time: " . date('Y-m-d H:i:s'), $headers);

			if ($sms_notification->delete())
			    $body.="\nNotification successfuly deleted!";
			else
			    $body.="\nNotification could not be deleted!";
		    }
		    else
			$body = "Something went wrong when acquiring data for the GSN!\nNotification ID: " . $sms_notification->notification_id;
		}
		else
		    $body = "There was no email notification request with Notification ID: " . $_REQUEST['notification_id'];

		try {
		    mail("hyracoidea@gmail.com", $subject, $start . "\n" . $body . "Notification request deleting process finished! Time: " . date('Y-m-d H:i:s'), $headers);
		} catch (Exception $ae) {
		    echo "Problem occured when sending email!";
		}
	    }
	}

	$this->render('userNotificationRequests');
    }

    /**
     * All watchdog timer requests - user managing
     */
    public function actionUserWatchdogRequests() {
	//$model = new UserEmailWatchdogRequests;
	//if administrator has chosen any watchdog request we need to provide this request (approve, decline)
	if (isset($_REQUEST['watchdog_id']) && (isset($_REQUEST['type']))) {
	    if ($_REQUEST['type'] != "SMS") {
		$headers = "From: " . Yii::app()->params['adminEmail'] . "\r\nReply-To: " . Yii::app()->params['adminEmail'];
		$subject = "Email WatchdogTimer approval/disapproval";
		$body = "";

		$start = "WatchdogTimer request deleting process started! Time: " . date('Y-m-d H:i:s');

		$email_notification = new ProdEmailWatchdogTimer;

		$email_notification = ProdEmailWatchdogTimer::model()->findByAttributes(array('watchdog_id' => $_REQUEST['watchdog_id']));
		//if we find a row that equals this one, we do nothing at all

		if (($email_notification) != null) { //if notification request exists proceed
		    //we first collect info on GSN server of this notification
		    $gsn_row = DiGsn::model()->find(array('select' => 'gsn_name, gsn_ip, notification_folder, notification_backup_folder, sftp_username, sftp_password', 'condition' => 'gsn_id=' . $email_notification->gsn_id));

		    if ($gsn_row != null) {
			try {
			    $sftp_obj = new SftpComponent($gsn_row['gsn_ip'], $gsn_row['sftp_username'], $gsn_row['sftp_password']);
			    $sftp_obj->connect();

			    try {
				//first we try to delete original notification file
				if ($sftp_obj->isDir($gsn_row['notification_folder']))
				    $body.="\nNotification backup folder exists!\n";
				else
				    $body.="\nNotification backup folder does not exist!\n";
				$sftp_obj->chdir($gsn_row['notification_folder']);
				$sftp_obj->removeFile($email_notification->xml_name . ".xml");
			    } catch (Exception $er) {
				$body = $body . "Primary watchdog timer was not succesfully removed. Please check the problem!\nError message " . $er->getMessage() . "\n";
			    }

			    try {
				//then we try to delete backup notification file
				if ($sftp_obj->isDir($gsn_row['notification_backup_folder']))
				    $body.="\nNotification backup folder exists!\n";
				else
				    $body.="\nNotification backup folder does not exist!\n";

				$sftp_obj->chdir($gsn_row['notification_backup_folder']);
				$sftp_obj->removeFile($email_notification->xml_name . ".xml");
			    } catch (Exception $er) {
				$body = $body . "Backup notification was not succesfully removed. Please check the problem!\nError message " . $er->getMessage() . "\n";
			    }
			    //if this was successful we need to inform our administrator about the action
			    if ($body == "")
				$body = "File was succesfully removed!\nGSN name: " . $gsn_row['gsn_name'] . "Watchdog ID: " . $email_notification->watchdog_id;
			} catch (Exception $e) {
			    $body = "File was NOT successfully removed! Please make a manual check on the problem!\nIt seems that the connection could not be established.\nWatchdog ID: " . $email_notification->watchdog_id . "\nError message: " . $e->getMessage();
			}

			//mail("hyracoidea@gmail.com", $subject, $start . "\n" . $body . "Notification request deleting process finished! Time: " . date('Y-m-d H:i:s'), $headers);

			if ($email_notification->delete())
			    $body.="\nWatchdogTimer successfuly deleted!";
			else
			    $body.="\nWatchdogTimer could not be deleted!";
		    }
		    else
			$body = "Something went wrong when acquiring data for the GSN!\nNotification ID: " . $email_notification->watchdog_id;
		}
		else
		    $body = "There was no email notification request with Notification ID: " . $_REQUEST['watchdog_id'];

		mail("hyracoidea@gmail.com", $subject, $start . "\n" . $body . "WatchdogTimer request deleting process finished! Time: " . date('Y-m-d H:i:s'), $headers);
	    }
	    else {
		$headers = "From: " . Yii::app()->params['adminEmail'] . "\r\nReply-To: " . Yii::app()->params['adminEmail'];
		$subject = "SMS Watchdog approval/disapproval";
		$body = "";
		//if administrator has chosen any notification request we need to provide this request (approve, decline)
		$start = "Watchdog timer request deleting process started! Time: " . date('Y-m-d H:i:s');

		$sms_notification = new ProdSmsWatchdogTimer;

		$sms_notification = ProdSmsWatchdogTimer::model()->findByAttributes(array('watchdog_id' => $_REQUEST['watchdog_id']));
		//if we find a row that equals this one, we do nothing at all

		if (($sms_notification) != null) { //if notification request exists proceed
		    //we first collect info on GSN server of this notification
		    $gsn_row = DiGsn::model()->find(array('select' => 'gsn_name, gsn_ip, notification_folder, notification_backup_folder, sftp_username, sftp_password', 'condition' => 'gsn_id=' . $sms_notification->gsn_id));

		    if ($gsn_row != null) {
			try {
			    $sftp_obj = new SftpComponent($gsn_row['gsn_ip'], $gsn_row['sftp_username'], $gsn_row['sftp_password']);
			    $sftp_obj->connect();

			    try {
				//first we try to delete original notification file
				if ($sftp_obj->isDir($gsn_row['notification_folder']))
				    $body.="\nNotification backup folder exists!\n";
				else
				    $body.="\nNotification backup folder does not exist!\n";
				$sftp_obj->chdir($gsn_row['notification_folder']);
				$sftp_obj->removeFile($sms_notification->xml_name . ".xml");
			    } catch (Exception $er) {
				$body = $body . "Primary watchdog was not succesfully removed. Please check the problem!\nError message " . $er->getMessage() . "\n";
			    }

			    try {
				//then we try to delete backup notification file
				if ($sftp_obj->isDir($gsn_row['notification_backup_folder']))
				    $body.="\nNotification backup folder exists!\n";
				else
				    $body.="\nNotification backup folder does not exist!\n";

				$sftp_obj->chdir($gsn_row['notification_backup_folder']);
				$sftp_obj->removeFile($sms_notification->xml_name . ".xml");
			    } catch (Exception $er) {
				$body = $body . "Backup notification was not succesfully removed. Please check the problem!\nError message " . $er->getMessage() . "\n";
			    }
			    //if this was successful we need to inform our administrator about the action
			    if ($body == "")
				$body = "File was succesfully removed!\nGSN name: " . $gsn_row['gsn_name'] . "Watchdog ID: " . $sms_notification->watchdog_id;
			} catch (Exception $e) {
			    $body = "File was NOT successfully removed! Please make a manual check on the problem!\nIt seems that the connection could not be established.\nWatchdog ID: " . $sms_notification->watchdog_id . "\nError message: " . $e->getMessage();
			}

			//mail("hyracoidea@gmail.com", $subject, $start . "\n" . $body . "Notification request deleting process finished! Time: " . date('Y-m-d H:i:s'), $headers);

			if ($sms_notification->delete())
			    $body.="\nWatchdogTimer successfuly deleted!";
			else
			    $body.="\nWatchdogTimer could not be deleted!";
		    }
		    else
			$body = "Something went wrong when acquiring data for the GSN!\nWatchdog ID: " . $sms_notification->watchdog_id;
		}
		else
		    $body = "There was no email notification request with Notification ID: " . $_REQUEST['watchdod_id'];

		mail("hyracoidea@gmail.com", $subject, $start . "\n" . $body . "WatchdogTimer request deleting process finished! Time: " . date('Y-m-d H:i:s'), $headers);
	    }
	}

	//$this->layout = 'watchdog_template';
	//$this->render('userWatchdogTimerRequests', array('model' => $model));
	$this->render('userWatchdogTimerRequests');
    }

    /**
     * Displays the user watchdog page
     */
    public function actionUserWatchdogTimer() {

	$model = new UserWatchdogTimer;

	//$this->layout = 'watchdog_template';

	$model->watchdog_type = ((int) !empty($_POST['UserWatchdogTimer']['watchdog_type']) ? $_POST['UserWatchdogTimer']['watchdog_type'] : ((int) !empty($_GET['watchdog_type']) ? $_GET['watchdog_type'] : 99999));

	$model->gsn_id = ((int) !empty($_POST['UserWatchdogTimer']['gsn_list']) ? $_POST['UserWatchdogTimer']['gsn_list'] : ((int) !empty($_GET['gsn_id']) ? $_GET['gsn_id'] : 99999));
	$model->sensor_id = ((int) !empty($_POST['UserWatchdogTimer']['sensor_list']) ? $_POST['UserWatchdogTimer']['sensor_list'] : ((int) !empty($_GET['sensor_id']) ? $_GET['sensor_id'] : 99999));

	$model->critical_period = ((int) !empty($_POST['UserWatchdogTimer']['critical_period']) ? $_POST['UserWatchdogTimer']['critical_period'] : ((int) !empty($_GET['critical_period']) ? $_GET['critical_period'] : null));

	$model->minimal_delay_between_emails = ((int) !empty($_POST['UserWatchdogTimer']['minimal_delay_between_emails']) ? $_POST['UserWatchdogTimer']['minimal_delay_between_emails'] : ((int) !empty($_GET['minimal_delay_between_emails']) ? $_GET['minimal_delay_between_emails'] : null));

	$model->email = ((string) !empty($_POST['UserWatchdogTimer']['email']) ? $_POST['UserWatchdogTimer']['email'] : ((int) !empty($_GET['email']) ? $_GET['email'] : null));
	$model->phone_number = ((string) !empty($_POST['UserWatchdogTimer']['phone_number']) ? $_POST['UserWatchdogTimer']['phone_number'] : ((int) !empty($_GET['phone_number']) ? $_GET['phone_number'] : null));

	//$date_not = new CDbExpression('NOW()');

	if (isset($_POST['UserWatchdogTimer'])) {
	    $model->attributes = $_POST['UserWatchdogTimer'];

	    if ($model->watchdog_type == 1) { //we are dealing with sms notification
		$model_notifications = new ProdSmsWatchdogTimer;

		$model_notifications->sensor_id = $model->sensor_id;

		$sensor_row = DiSensors::model()->find(array(
			    'select' => 'sensor_user_name, gsn_id',
			    'condition' => 'sensor_id = ' . $model->sensor_id));
		$model_notifications->sensor_name = (string) $sensor_row['sensor_user_name'];
		$model_notifications->gsn_id = $sensor_row['gsn_id'];
		$model_notifications->user_id = Yii::app()->user->id;
		$model_notifications->critical_period = $model->critical_period;
		$model_notifications->minimal_delay_between_emails = $model->minimal_delay_between_emails;
		$model_notifications->phone = $model->phone_number;

		$model_notifications->xml_name = "W_" . Yii::app()->user->username . "_" . $model_notifications->sensor_name . "2_" . strtotime(date('m/d/Y h:i:s a', time()));


		//$model_notifications->is_active = '0';
		$body = "prod_user_id = " . $model_notifications->user_id .
			"\nUsername = " . Yii::app()->user->username .
			"\nSensor_id = " . $model_notifications->sensor_id .
			"\nCritical period = " . $model_notifications->critical_period .
			"\nMinimal delay between SMS = " . $model_notifications->minimal_delay_between_emails .
			"\nWatchdog type = " . $model->watchdog_type .
			"\nPhone = " . $model_notifications->phone
		;
	    } else if ($model->watchdog_type == 2) { //we are dealing with email notification
		$model_notifications = new ProdEmailWatchdogTimer;

		//$model_notification->time_notification_asked = new CDbExpression('NOW()');

		$model_notifications->sensor_id = $model->sensor_id;
		$sensor_row = DiSensors::model()->find(array(
			    'select' => 'sensor_user_name,gsn_id',
			    'condition' => 'sensor_id = ' . $model->sensor_id));
		$model_notifications->sensor_name = (string) $sensor_row['sensor_user_name'];
		$model_notifications->gsn_id = $sensor_row['gsn_id'];
		//$model_notifications->gsn_id = $model->gsn_id;
		$model_notifications->user_id = Yii::app()->user->id;
		$model_notifications->critical_period = $model->critical_period;
		$model_notifications->minimal_delay_between_emails = $model->minimal_delay_between_emails;
		$model_notifications->email = $model->email;

		$model_notifications->xml_name = "W_" . Yii::app()->user->username . "_" . $model_notifications->sensor_name . "1_" . strtotime(date('m/d/Y h:i:s a', time()));

		//$model_notifications->is_active = '0';
		$body = "user_id = " . $model_notifications->user_id .
			"\nUsername = " . Yii::app()->user->username .
			"\nSensor_id = " . $model_notifications->sensor_id .
			"\nCritical period = " . $model_notifications->critical_period .
			"\nMinimal delay between emails = " . $model_notifications->minimal_delay_between_emails .
			"\nWatchdog type = " . $model->watchdog_type .
			"\nEmail = " . $model->email
		;
	    }
	    else
		$body = "Nema tog watchdog timer Type-a";

	    $headers = "From: " . Yii::app()->params['adminEmail'] . "\r\nReply-To: " . Yii::app()->params['adminEmail'];
	    $subject = "WatchdogTimer info";
	    //$body = "Login username : ".$this->username."!\r\n\nLogin password : ".$this->password;
	    //mail("hyracoidea@gmail.com", $subject, $body, $headers);
	    if ($model->validate()) {
		if ($model_notifications->save()) {
		    $headers = "From: " . Yii::app()->params['adminEmail'] . "\r\nReply-To: " . Yii::app()->params['adminEmail'];
		    $subject = "WatchdogTimer info";
		    $body.="\nWatchdogTimer successfully saved with given parameters";
		    //$body = "Login username : ".$this->username."!\r\n\nLogin password : ".$this->password;
		    mail("leonard.beus@fer.hr", $subject, $body, $headers);
		} else {
		    $headers = "From: " . Yii::app()->params['adminEmail'] . "\r\nReply-To: " . Yii::app()->params['adminEmail'];
		    $subject = "WatchdogTimer info";
		    //$body = "Login username : ".$this->username."!\r\n\nLogin password : ".$this->password;
		    mail("leonard.beus@fer.hr", $subject, "Neuspjesno spremanje WatchdogTimer zahtjeva", $headers);
		}
		//Yii::app()->user->setFlash('notifications','Thank you for your time to fill in the form. Your request has been activated.');
		$this->refresh();
	    }
	}
	$this->render('userWatchdogTimer', array('model' => $model));
    }

    /**
     * Displays the main page to roam over all the sesnors you have privilege to see
     */
    public function actionUserSensors() {
	$model = new UserSensors;

	$this->render('userSensors', array('model' => $model));
    }

    /**
     * Actions for the notification dropdown menus
     */
    public function actionUserGraphViewerDynamicSensors() {
	$data = DiSensors::model()->findAll('gsn_id=:parent_id',
			array(':parent_id' => (int) $_POST['UserGraphViewer']['gsn_list']));


	$data = CHtml::listData($data, 'sensor_id', 'sensor_user_name');
	echo CHtml::tag('option', array('value' => 0), 'Select', true);
	foreach ($data as $id => $value) {
	    echo CHtml::tag('option', array('value' => $id), CHtml::encode($value), true);
	}
    }

    public function actionUserGraphViewerDynamicUnits() {
	$data = DiUnits::model()->with(array(
		    'fSensorTypes' => array(
			'select' => false,
			'joinType' => 'INNER JOIN',
		    ),
		))->findAll('"fSensorTypes".sensor_id=:parent_id',
			array(':parent_id' => (int) $_POST['UserGraphViewer']['sensor_list']));

	$data = CHtml::listData($data, 'unit_id', 'unit_name');
	echo CHtml::tag('option', array('value' => 0), 'Select', true);
	foreach ($data as $id => $value) {
	    echo CHtml::tag('option', array('value' => $id), CHtml::encode($value), true);
	}
    }

    public function actionUserGraphViewer() {
	$model = new UserGraphViewer;

	$model->selectedGsn = (int) (isset($_POST['UserGraphViewer']['gsn_list']) ? ($_POST['UserGraphViewer']['gsn_list']) : "-1");
	$model->selectedSensor = (int) (isset($_POST['UserGraphViewer']['sensor_list']) ? ($_POST['UserGraphViewer']['sensor_list']) : "-1");
	$model->selectedUnit = (int) (isset($_POST['UserGraphViewer']['unit_list']) ? ($_POST['UserGraphViewer']['unit_list']) : "-1");
	$model->submitedForm = (isset($_POST['submit_button']) ? true : false);

	$model->selectedEndDate = (isset($_POST['UserGraphViewer']['chosenEndDate']) ? $_POST['UserGraphViewer']['chosenEndDate'] : "3000-01-01");
	$model->selectedStartDate = (isset($_POST['UserGraphViewer']['chosenStartDate']) ? $_POST['UserGraphViewer']['chosenStartDate'] : "3000-01-01");

	$this->render('userGraphViewer', array('model' => $model));
    }

}

?>
