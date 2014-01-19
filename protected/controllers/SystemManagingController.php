<?php

class SystemManagingController extends Controller {

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
     * Manages heating control system.
     */
    public function actionHeatingControl() {
	$model = new GsnSystemManaging;

	//$new = new EmailSending();
	//$new->sendHtml('Testing','hyracoidea@gmail.com');

	if (!isset(Yii::app()->user->selectedGsn)) {
	    Yii::app()->user->setState('selectedGsn', -99);
	} else {
	    $model->selectedGsn = Yii::app()->user->selectedGsn;
	}
	$this->render('gsnSystemManaging', array('model' => $model));
    }

    public function actionManagingConfigFile() {
	if
	(
		!(
		!isset($_POST['fan_1']) || !isset($_POST['fan_2']) || !isset($_POST['fan_3']) || !isset($_POST['fan_4']) || !isset($_POST['fan_5'])
		|| !isset($_POST['heater_1']) || !isset($_POST['heater_2']) || !isset($_POST['heater_3']) || !isset($_POST['heater_4']) || !isset($_POST['heater_5'])
		|| !isset($_POST['external_temp_limit'])
		|| !isset($_POST['internal_temp_limit1'])
		|| !isset($_POST['internal_temp_limit2'])
		|| !isset($_POST['internal_temp_limit3'])
		|| !isset($_POST['rabbit_ip'])
		|| !isset($_POST['free_server_port'])
		|| !isset($_POST['auto_control'])
		|| !isset($_POST['manual_fan'])
		|| !isset($_POST['manual_heater'])
		|| !isset($_POST['air_intake'])
		|| !isset($_POST['gsn_id'])
		|| !isset($_POST['recipients_emails'])
		)) {
	    if (!($_POST['fan_1'] <= 5 && $_POST['fan_1'] >= 0)
		    || !($_POST['fan_2'] <= 5 && $_POST['fan_2'] >= 0)
		    || !($_POST['fan_3'] <= 5 && $_POST['fan_3'] >= 0)
		    || !($_POST['fan_4'] <= 5 && $_POST['fan_4'] >= 0)
		    || !($_POST['fan_5'] <= 5 && $_POST['fan_5'] >= 0)
		    || ((int) $_POST['internal_temp_limit1']) < ((int) $_POST['internal_temp_limit2'])
		    || ((int) $_POST['internal_temp_limit2']) < ((int) $_POST['internal_temp_limit3'])
		    || ((int) $_POST['internal_temp_limit1']) < ((int) $_POST['internal_temp_limit3'])
	    ) {
		$model = new GsnSystemManaging;

		if (((int) $_POST['internal_temp_limit1']) < ((int) $_POST['internal_temp_limit2'])
			|| ((int) $_POST['internal_temp_limit2']) < ((int) $_POST['internal_temp_limit3'])
			|| ((int) $_POST['internal_temp_limit1']) < ((int) $_POST['internal_temp_limit3']))
		    $model->creatingMessage = "<b>MESSAGE: </b>Internal temperature limit need to go in the following order<br/>internal_temp_limit1 <b>></b> internal_temp_limit2 <b>></b> internal_temp_limit3";
		else
		    $model->creatingMessage = "<b>MESSAGE:</b> Please try again to input your information as you did not fill in valid information!<br/><br/>";

		if (!isset(Yii::app()->user->selectedGsn))
		    Yii::app()->user->setState('selectedGsn', -99);
		$this->render('gsnSystemManaging', array('model' => $model));
	    }
	    else {
		$all_recipients = array();

		if (isset($_POST['recipient_number'])) {
		    //echo "Imamo ih ".$_POST['recipient_number'];
		    if ($_POST['recipient_number'] >= 0) {
			for ($i = 0; $i <= $_POST['recipient_number']; $i++) {
			    if (isset($_POST['recipient_' . $i])) {
				array_push($all_recipients, $_POST['recipient_' . $i]);
			    }
			}
		    }
		}

		if (empty($all_recipients)) {
		    $all_emails = explode("!!", $_POST['recipients_emails']);

		    foreach ($all_emails as $email)
			array_push($all_recipients, $email);
		}

		$model = new GsnSystemManaging;

		//everything went ok with double-checking user information, and now we can proceed with creating xml
		$xml_file = $model->controlXMLgenerating($_POST['external_temp_limit']
				, $_POST['internal_temp_limit1']
				, $_POST['internal_temp_limit2']
				, $_POST['internal_temp_limit3']
				, $_POST['fan_1'], $_POST['fan_2'], $_POST['fan_3'], $_POST['fan_4'], $_POST['fan_5']
				, $_POST['heater_1'], $_POST['heater_2'], $_POST['heater_3'], $_POST['heater_4'], $_POST['heater_5']
				, $_POST['rabbit_ip'], $_POST['free_server_port'], $_POST['auto_control'], $_POST['manual_fan'], $_POST['manual_heater']
				, $_POST['air_intake']
				, $all_recipients);

		if (!$xml_file) {
		    /*
		     * Creating XML file went wrong, please check the problem :)
		     */
		    $model->creatingMessage = "<b>MESSAGE:</b>Problem occured while creating XML config file. Please contact administrator for further support!<br/><br/>";
		    if (!isset(Yii::app()->user->selectedGsn))
			Yii::app()->user->setState('selectedGsn', -99);
		    $this->render('gsnSystemManaging', array('model' => $model));
		}
		else {
		    $gsn = DiGsn::model()->findByPk($_POST['gsn_id']);

		    if (!empty($gsn)) {
			$model->creatingMessage = "<b>MESSAGE:</b>We have successfuly found GSN server!</br>";

			$xml_file = utf8_encode($xml_file);

			if (strcmp($gsn['gsn_ip'], "127.0.0.1")==0){
			    $file_w = fopen($gsn['gsn_home_folder'] . '\passiveHeating\config.xml', 'w');
			    fwrite($file_w, $xml_file);
			    $model->creatingMessage = "Your process has been saved correctly!";

			    if (!isset(Yii::app()->user->selectedGsn))
				Yii::app()->user->setState('selectedGsn', -99);
			    $this->render('gsnSystemManaging', array('model' => $model));
			}
			else
			{
			$sftp_obj = new SftpComponent($gsn['gsn_ip'], $gsn['sftp_username'], $gsn['sftp_password']);
			$sftp_obj->connect();
			$message = "Trying to connect to GSN server at folder " . $gsn['gsn_home_folder'] . '/passiveHeating';
			try {
			    if ($sftp_obj->isDir($gsn['gsn_home_folder'] . '/passiveHeating'))
				$message.="\nPassive heating folder exists!\n";
			    else
				$message.="\nPassive heating folder does not exist!\n";

			    $sftp_obj->chdir($gsn['gsn_home_folder'] . '/passiveHeating');

			    $sftp_obj->sendFile($xml_file, 'config.xml');
			    $message .= "\nConfig file was successfuly deployed and updated!";
			    $error = false;
			} catch (Exception $er) {
			    $message .= "\nERROR occured while saving XML file or trying to change folder!\nError message: " . $er->getMessage();
			    $error = true;
			}

			if (!$error) {
			    $model->creatingMessage = "<b>MESSAGE:</b>Your changes have successfuly been saved!<br/><br/>";
			}
			else
			    $model->creatingMessage = $message;

			if (!isset(Yii::app()->user->selectedGsn))
			    Yii::app()->user->setState('selectedGsn', -99);
			$this->render('gsnSystemManaging', array('model' => $model));
			}
		    }
		    else {

			$model->creatingMessage = "<b>MESSAGE:</b>It seems that GSN server you were trying to manage is currently not working properly. Please try again and, if the problem persists, contact administrator for further help.<br/><br/>";
			if (!isset(Yii::app()->user->selectedGsn))
			    Yii::app()->user->setState('selectedGsn', -99);
			$this->render('gsnSystemManaging', array('model' => $model));
		    }
		}
	    }
	}
	else {
	    $model = new GsnSystemManaging;
	    $model->creatingMessage = $message . "<b>MESSAGE:</b>Some data you have provided proved to be invalid. Please try again or contact our administrator.<br/><br/>";
	    if (!isset(Yii::app()->user->selectedGsn))
		Yii::app()->user->setState('selectedGsn', -99);
	    $this->render('gsnSystemManaging', array('model' => $model));
	}
    }

    /*
     * Renders data directly from GSN via HTTP handler /gsn
     * Update interval is 10 seconds, and it is declared in refresh script in main.php
     */

    public function actionManagingLiveStream() {
	$model = new GsnSystemManaging;

	if (Yii::app()->user->selectedGsn != -99) {
	    if (isset($_POST['GsnSystemManaging']['gsn_id'])) {
		$selected_gsn = $_POST['GsnSystemManaging']['gsn_id'];
		Yii::app()->user->setState('selectedGsn', $selected_gsn);
	    }
	    else
		$selected_gsn = Yii::app()->user->selectedGsn;
	    $model->liveDataOutput($selected_gsn);
	}
    }

    public function actionManagingRefreshTable() {
	$model = new GsnSystemManaging;

	if (Yii::app()->user->selectedGsn != -99) {
	    if (isset($_POST['GsnSystemManaging']['gsn_id'])) {
		$selected_gsn = $_POST['GsnSystemManaging']['gsn_id'];
		Yii::app()->user->setState('selectedGsn', $selected_gsn);
	    }
	    else
		$selected_gsn = Yii::app()->user->selectedGsn;

	    if ($model->gatherGsnConfigStats($selected_gsn, $return_message, $externalTempLimit, $internalTempLimit1, $internalTempLimit2, $internalTempLimit3, $fan_1, $fan_2, $fan_3, $fan_4, $fan_5, $heater_1, $heater_2, $heater_3, $heater_4, $heater_5, $rabbit_ip, $free_server_port, $auto_control, $manual_fan, $manual_heater, $air_intake, $emails))
		$model->configTableOutput($externalTempLimit, $internalTempLimit1, $internalTempLimit2, $internalTempLimit3, $fan_1, $fan_2, $fan_3, $fan_4, $fan_5, $heater_1, $heater_2, $heater_3, $heater_4, $heater_5, $rabbit_ip, $free_server_port, $auto_control, $manual_fan, $manual_heater, $air_intake, $emails, $selected_gsn);
	    else {
		echo (isset($return_message) ? $return_message : "Unable to resolve the error while reading heating config file!<br/>");
	    }
	} else {
	    echo "Please choose one of the GSN servers above!";
	}
    }

    public function actionManagingManualControl() {
	$model = new GsnSystemManaging;

	if (isset($_POST['auto_control'])) {
	    try {
		if (isset($_POST['gsn_id'])) {
		    $selected_gsn = $_POST['gsn_id'];
		    Yii::app()->user->setState('selectedGsn', $selected_gsn);
		}
		else
		    $selected_gsn = Yii::app()->user->selectedGsn;

		$gsn = DiGsn::model()->findByPk($selected_gsn);

		if (!empty($gsn)) {
		    if ($gsn['port_ssl']) {
			$GSN_url = "https://" . $gsn['username'] . ":" . $gsn['password'] . "@" . $gsn['gsn_ip'] . ":" . $gsn['port_ssl'];
		    } else {
			$GSN_url = "http://" . $gsn['username'] . ":" . $gsn['password'] . "@" . $gsn['gsn_ip'] . ":" . $gsn['gsn_port'];
		    }

		    $gsnHandler = $GSN_url . '/passiveheating/autocontrol';

		    if (@fopen($gsnHandler, "r")) {
			//open xml file
			$xml_file = simplexml_load_file($gsnHandler);
			//$model->creatingMessage = "GSNURL: " . $gsnHandler;
			foreach ($xml_file->children() as $response) {
			    //here we should state what should be done
			    if (strcmp($response->getName(), "status") == 0) {
				//if status is something we need to start autocontrol
				$model->creatingMessage = "Your action was processed and returned status: <b>" . $response . "</b>!<br/>";
			    }
			    if (strcmp($response->getName(), "description") == 0) {
				//if status is something we need to start autocontrol
				$model->creatingMessage .= "<b>DESCRIPTION:</b> " . $response . "<br/>";
			    }
			}
		    }
		    else
			$model->creatingMessage = "Unable to process your command! GSN could be down for maintenence Please try again!";
		}
	    } catch (Exception $ae) {
		$model->creatingMessage .= "Unexpected error occured while processing your request! Please try again or contact administrator for help!<br/>";
	    }

	    $this->render('gsnSystemManaging', array('model' => $model));
	    return;
	} else
	if (isset($_POST['save'])) {
	    try {
		if (isset($_POST['gsn_id'])) {
		    $selected_gsn = $_POST['gsn_id'];
		    Yii::app()->user->setState('selectedGsn', $selected_gsn);
		}
		else
		    $selected_gsn = Yii::app()->user->selectedGsn;

		$gsn = DiGsn::model()->findByPk($selected_gsn);

		if (!empty($gsn)) {
		    if ($gsn['port_ssl']) {
			$GSN_url = "https://" . $gsn['username'] . ":" . $gsn['password'] . "@" . $gsn['gsn_ip'] . ":" . $gsn['port_ssl'];
		    } else {
			$GSN_url = "http://" . $gsn['username'] . ":" . $gsn['password'] . "@" . $gsn['gsn_ip'] . ":" . $gsn['gsn_port'];
		    }

		    $gsnHandler = $GSN_url . '/passiveheating/control';

		    if (isset($_POST['manual_fan']) || isset($_POST['manual_heater'])) {
			if ((isset($_POST['manual_fan']) && ($_POST['manual_fan'] < 6) && ($_POST['manual_fan'] >= 0)) && (isset($_POST['manual_heater'])))
			    $gsnHandler .= "?fan=" . $_POST['manual_fan'] . "&heater=" . $_POST['manual_heater'];
			else if (isset($_POST['manual_heater']))
			    $gsnHandler .= "?heater=" . $_POST['manual_heater'];

			//$model->creatingMessage = $gsnHandler . " Heater: " .$_POST['manual_heater']." FAN: ". $_POST['manual_fan'];

			if (@fopen($gsnHandler, "r")) {
			    //open xml file
			    $xml_file = simplexml_load_file($gsnHandler);
			    //$model->creatingMessage = "GSNURL: " . $gsnHandler;
			    foreach ($xml_file->children() as $response) {
				//here we should state what should be done
				if (strcmp($response->getName(), "status") == 0) {
				    //if status is something we need to start autocontrol
				    $model->creatingMessage = "Your action was processed and returned status: <b>" . $response . "</b>!<br/>";
				}
				if (strcmp($response->getName(), "description") == 0) {
				    //if status is something we need to start autocontrol
				    $model->creatingMessage .= "<b>DESCRIPTION:</b> " . $response . "<br/>";
				}
			    }
			}
		    } else {
			$model->creatingMessage = "It seems like no information was provided for either fan or heater setup. Please try again!<br/>";
		    }
		}
	    } catch (Exception $ae) {
		$model->creatingMessage .= "Unexpected error occured while processing your request! Please try again or contact administrator for help!<br/>";
	    }

	    $this->render('gsnSystemManaging', array('model' => $model));
	    return;
	} else
	if (isset($_POST['air_intake']) && (isset($_POST['intake_method']))) {
	    try {
		if (isset($_POST['gsn_id'])) {
		    $selected_gsn = $_POST['gsn_id'];
		    Yii::app()->user->setState('selectedGsn', $selected_gsn);
		}
		else
		    $selected_gsn = Yii::app()->user->selectedGsn;

		$gsn = DiGsn::model()->findByPk($selected_gsn);

		if (!empty($gsn)) {
		    if ($gsn['port_ssl']) {
			$GSN_url = "https://" . $gsn['username'] . ":" . $gsn['password'] . "@" . $gsn['gsn_ip'] . ":" . $gsn['port_ssl'];
		    } else {
			$GSN_url = "http://" . $gsn['username'] . ":" . $gsn['password'] . "@" . $gsn['gsn_ip'] . ":" . $gsn['gsn_port'];
		    }

		    $gsnHandler = $GSN_url . '/passiveheating/air?intake=' . $_POST['intake_method'];

		    if (@fopen($gsnHandler, "r")) {
			//open xml file
			$xml_file = simplexml_load_file($gsnHandler);
			//$model->creatingMessage = "GSNURL: " . $gsnHandler;
			foreach ($xml_file->children() as $response) {
			    //here we should state what should be done
			    if (strcmp($response->getName(), "status") == 0) {
				//if status is something we need to start autocontrol
				$model->creatingMessage = "Your action was processed and returned status: <b>" . $response . "</b>!<br/>";
			    }
			    if (strcmp($response->getName(), "description") == 0) {
				//if status is something we need to start autocontrol
				$model->creatingMessage .= "<b>DESCRIPTION:</b> " . $response . "<br/>";
			    }
			}
		    }
		    else
			$model->creatingMessage = "Unable to process your command! GSN could be down for maintenence Please try again!";
		}
		else
		    $model->creatingMessage = "GSN was not found! Please try again or contact our administrator!<br/>";
	    } catch (Exception $ae) {
		$model->creatingMessage .= "Unexpected error occured while processing your request! Please try again or contact administrator for help!<br/>";
	    }

	    $this->render('gsnSystemManaging', array('model' => $model));
	    return;
	} else {
	    $model->creatingMessage = "Unknown request processed, we apologize for the inconvinience!";
	    $this->render('gsnSystemManaging', array('model' => $model));
	}
    }

    public function actionManagingPartialView() {
	$model = new GsnSystemManaging;

	if (isset($_POST['GsnSystemManaging']['gsn_id']) || Yii::app()->user->selectedGsn != -99) {
	    if (isset($_POST['GsnSystemManaging']['gsn_id'])) {
		$selected_gsn = $_POST['GsnSystemManaging']['gsn_id'];
		Yii::app()->user->setState('selectedGsn', $selected_gsn);
	    }
	    else
		$selected_gsn = Yii::app()->user->selectedGsn;

	    $model->liveDataOutput($selected_gsn);

	    if ($model->gatherGsnConfigStats($selected_gsn, $return_message, $externalTempLimit, $internalTempLimit1, $internalTempLimit2, $internalTempLimit3, $fan_1, $fan_2, $fan_3, $fan_4, $fan_5, $heater_1, $heater_2, $heater_3, $heater_4, $heater_5, $rabbit_ip, $free_server_port, $auto_control, $manual_fan, $manual_heater, $air_intake, $emails))
		$model->configTableOutput($externalTempLimit, $internalTempLimit1, $internalTempLimit2, $internalTempLimit3, $fan_1, $fan_2, $fan_3, $fan_4, $fan_5, $heater_1, $heater_2, $heater_3, $heater_4, $heater_5, $rabbit_ip, $free_server_port, $auto_control, $manual_fan, $manual_heater, $air_intake, $emails, $selected_gsn);
	    else {
		echo (isset($return_message) ? $return_message : "Unable to resolve the error while reading heating config file!<br/>");
	    }
	} else {
	    echo "Please choose one of the GSN servers above!";
	}
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

}

?>
