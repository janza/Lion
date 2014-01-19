<?php

class GsnSystemManaging extends CFormModel {

    public $selectedGsn;
    public $gsn_id;
    public $creatingMessage = "";

    public function controlXMLgenerating(
    $externalTempLimit
    , $internalTempLimit1
    , $internalTempLimit2
    , $internalTempLimit3
    , $fan1, $fan2, $fan3, $fan4, $fan5
    , $heater1, $heater2, $heater3, $heater4, $heater5
    , $rabbit_ip
    , $free_server_port
//state concerning variables
    , $auto_control
    , $manual_fan
    , $manual_heater
    , $air_intake
    , $recipients_emails
    ) {
	$email_array = $recipients_emails;
	$conf = new SimpleXMLElement('<config></config>');
//$conf = $xml->addChild('config');
	/*
	 * <externalTempLimit>0</externalTempLimit>
	  <internalTempLimit1>5</internalTempLimit1>
	  <internalTempLimit2>2</internalTempLimit2>
	  <internalTempLimit3>-2</internalTempLimit3>
	 */
	$core = $conf->addChild('core-parameters');
	$state = $conf->addChild('state');

	$notifications = $conf->addChild('notifications');

	$ext = $core->addChild('externalTempLimit', $externalTempLimit);
	$int1 = $core->addChild('internalTempLimit1', $internalTempLimit1);
	$int2 = $core->addChild('internalTempLimit2', $internalTempLimit2);
	$int3 = $core->addChild('internalTempLimit3', $internalTempLimit3);

	$state1 = $core->addChild('state1');
	$fan_xml_1 = $state1->addChild('fan', $fan1);
	$heater_xml_1 = $state1->addChild('heater', $heater1);

	$state2 = $core->addChild('state2');
	$fan_xml_2 = $state2->addChild('fan', $fan2);
	$heater_xml_2 = $state2->addChild('heater', $heater2);

	$state3 = $core->addChild('state3');
	$fan_xml_3 = $state3->addChild('fan', $fan3);
	$heater_xml_3 = $state3->addChild('heater', $heater3);

	$state4 = $core->addChild('state4');
	$fan_xml_4 = $state4->addChild('fan', $fan4);
	$heater_xml_4 = $state4->addChild('heater', $heater4);

	$state5 = $core->addChild('state5');
	$fan_xml_5 = $state5->addChild('fan', $fan5);
	$heater_xml_5 = $state5->addChild('heater', $heater5);

	$rabbit = $core->addChild('rabbit-ip', $rabbit_ip);
	$free_server_port_xml = $core->addChild('free-server-port', $free_server_port);
//$notifications = $conf->addChild('core-parameters');

	$auto_xml_control = $state->addChild('auto-control', $auto_control);
	$manual_xml_fan = $state->addChild('manual-fan', $manual_fan);
	$manual_xml_heater = $state->addChild('manual-heater', $manual_heater);

	if (!$air_intake) {
//if we do not have air intake system, then nothing
	} else {
//else, we need to save the status
	    $air_xml_intake = $state->addChild('air-intake', $air_intake);
	}

	if ($recipients_emails != "") {
	    foreach ($email_array as $email) {
		$notifications_email = $notifications->addChild('email', $email);
	    }
	}
	else
	    $notifications_email = $notifications->addChild('email', "");

	return $conf->asXML();
    }

    public function configTableOutput(
    $externalTempLimit
    , $internalTempLimit1
    , $internalTempLimit2
    , $internalTempLimit3
    , $fan_1, $fan_2, $fan_3, $fan_4, $fan_5
    , $heater_1, $heater_2, $heater_3, $heater_4, $heater_5
    , $rabbit_ip
    , $free_server_port
//state concerning variables
    , $auto_control
    , $manual_fan
    , $manual_heater
    , $air_intake
    , $emails
    , $selected_gsn
    ) {
	echo '<br/><div id="control_form" class="control_form">';
	echo '<div style="border-top-style:solid;">';
	echo "<h3><b>System state managment</b></h3><br/>";
	echo '<form method="post" action="managingManualControl">';
	if ($auto_control == '1') {
	    echo "Auto-control is currently active!<br/><br/>";
	    echo "To take control, simply set the following parameters as you wish and submit your choice. Auto control will automatically be stoped.<br/>";
	} else {
//echo $current_status['manual-heater'];
	    echo "User control is currently active!<br/><br/>";
	    echo "Fan status: " . (($manual_fan == '0') ? 'Not working' : 'Working, current power is ' . $manual_fan) . "<br/>";
	    echo "Heater status: " . (($manual_heater == '0') ? 'Not working' : 'Working') . '<br/><br/>';
	    echo "If you want to activate auto-control by the parameters below, press: " . CHtml::submitButton('Auto control', array('id' => 'button_auto_control', 'name' => 'auto_control')) . "<br/>";
	}

	echo '<div class="control_subsystem"><label class="label_inline" for="name">Fan power: </label><input type="text" name="manual_fan" id="manual_fan" value="' . $manual_fan . '" readonly/><div class="inc button">+</div><div class="dec button">-</div></div>';
	echo "<p>Heater status: " . (($manual_heater == '1') ? '<input type="radio" name="manual_heater" value="1" checked/>On<input type="radio" name="manual_heater" value="0"/>Off' : '<input type="radio" name="manual_heater" value="1" />On<input type="radio" name="manual_heater" value="0" checked/>Off') . "</p>";
	echo '<input type="hidden" name="gsn_id" value="' . $selected_gsn . '"/>';
	echo CHtml::submitButton('Save configuration', array('name' => 'save'));
	echo "</form>";
	echo "</div>";

//echo '<div id="control_form" class="control_form">';
	echo '<br/><div style="border-top-style:solid;">';
	echo "<h3><b>Air-intake managment</b></h3><br/>";
	if (!$air_intake) {
	    echo "This system does not support air-intake subsystem!<br/><br/>";
	} else {
	    echo '<form method="post" action="managingManualControl">';
	    echo "Air intake is currently set to: <b>" . $air_intake . "</b></br>";
	    echo '<input type="hidden" name="gsn_id" value="' . $selected_gsn . '"/>';
	    echo '<input type="hidden" name="intake_method" value="' . ((strcmp($air_intake, "normal") == 0) ? "override" : "normal") . '"/>';
	    echo "If you want to change air-intake method press: " . CHtml::submitButton('Air-intake change', array('id' => 'button_air_intake', 'name' => 'air_intake'));
	    echo '</form>';
	}
	echo "</div>";
//echo "</div>";

	$all_recipients = "";

	echo '<br/><div style="border-top-style:solid;">';
	echo '<h3><b>Configuration file for auto control</b></h3><br/>';
	echo "Below is a detailed overview on configuration file for auto control on temperature measured on GSN server. This system is specified for research purposes and should be used as such.<br/>";
	echo '<form method="post" action="managingConfigFile">';
	echo '<table id="ver-minimalist" summary="Above is the managment system for chosen GSN">';
//echo '<thead><tr><th scope=\'col\'></th><th scope=\'col\'></th><th scope=\'col\'></th></tr></thead>';
	echo '<tfoot>
		<td colspan=\'3\'><em>Above is the managment system for chosen GSN</em></td>
		</tfoot>';
	echo '<tbody>';
	echo '<tr>
		<td style="color:black; font-weight:bold; width:160px;">Temperature states (change according to your needs)</td>
		<td><div class="roundedBox" id="type1"><div class="control_subsystem"><label for="name">External temperature limit<br/></label> <input type="text" name="external_temp_limit" id="external_temp_limit" value="' . $externalTempLimit . '" readonly/><div class="inc button_temp">+</div><div class="dec button_temp">-</div></div><div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div></div></td>
		<td></td>
	      </tr>';
	echo '<tr>
		<td><div class="roundedBox" id="type1"><div class="control_subsystem"><label for="name">Internal temperature limit 1<br/></label> <input type="text" name="internal_temp_limit1" id="internal_temp_limit1" value="' . $internalTempLimit1 . '" readonly/><div class="inc button_temp">+</div><div class="dec button_temp">-</div></div><div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div></div></td>
		<td><div class="roundedBox" id="type2"><b>STATE 2</b><div class="control_subsystem"><label for="name">Fan: </label> <input type="text" name="fan_2" id="fan_2" value="' . $fan_2 . '" readonly/><div class="inc button">+</div><div class="dec button">-</div></div>Heater: ' . (($heater_2 == 1) ? '<input type="radio" name="heater_2" value="1" checked/>On<input type="radio" name="heater_2" value="0"/>Off' : '<input type="radio" name="heater_2" value="1" />On<input type="radio" name="heater_2" value="0" checked/>Off') . '<div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div></div></td>
		<td rowspan=\'4\'><div class="roundedBox" id="type2"><b>STATE 1</b><br/><div class="control_subsystem"><label for="name">Fan: </label> <input type="text" name="fan_1" id="fan_2" value="' . $fan_1 . '" readonly/><div class="inc button">+</div><div class="dec button">-</div></div>Heater: ' . (($heater_1 == 1) ? '<input type="radio" name="heater_1" value="1" checked/>On<input type="radio" name="heater_1" value="0"/>Off' : '<input type="radio" name="heater_1" value="1" />On<input type="radio" name="heater_1" value="0" checked/>Off') . '<div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div></div></td>
	      </tr>';
	echo '<tr>
		<td><div class="roundedBox" id="type1"><div class="control_subsystem"><label for="name">Internal temperature limit 2<br/></label> <input type="text" name="internal_temp_limit2" id="internal_temp_limit2" value="' . $internalTempLimit2 . '" readonly/><div class="inc button_temp">+</div><div class="dec button_temp">-</div></div><div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div></div></td>
		<td><div class="roundedBox" id="type2"><b>STATE 3</b><div class="control_subsystem"><label for="name">Fan: </label> <input type="text" name="fan_3" id="fan_3" value="' . $fan_3 . '" readonly/><div class="inc button">+</div><div class="dec button">-</div></div>Heater: ' . (($heater_3 == 1) ? '<input type="radio" name="heater_3" value="1" checked/>On<input type="radio" name="heater_3" value="0"/>Off' : '<input type="radio" name="heater_3" value="1" />On<input type="radio" name="heater_3" value="0" checked/>Off') . '<div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div></div></td>
		<td></td>
	      </tr>';
	echo '<tr>
		<td><div class="roundedBox" id="type1"><div class="control_subsystem"><label for="name">Internal temperature limit 3<br/></label> <input type="text" name="internal_temp_limit3" id="internal_temp_limit3" value="' . $internalTempLimit3 . '" readonly/><div class="inc button_temp">+</div><div class="dec button_temp">-</div></div><div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div></div></td>
		<td><div class="roundedBox" id="type2"><b>STATE 4</b><div class="control_subsystem"><label for="name">Fan: </label> <input type="text" name="fan_4" id="fan_4" value="' . $fan_4 . '" readonly/><div class="inc button">+</div><div class="dec button">-</div></div>Heater: ' . (($heater_4 == 1) ? '<input type="radio" name="heater_4" value="1" checked/>On<input type="radio" name="heater_4" value="0"/>Off' : '<input type="radio" name="heater_4" value="1" />On<input type="radio" name="heater_4" value="0" checked/>Off') . '<div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div></div></td>
		<td></td>
	      </tr>';
	echo '<tr>
		<td></td>
		<td><div class="roundedBox" id="type2"><b>STATE 5</b><div class="control_subsystem"><label for="name">Fan: </label> <input type="text" name="fan_5" id="fan_5" value="' . $fan_5 . '" readonly/><div class="inc button">+</div><div class="dec button">-</div></div>Heater: ' . (($heater_5 == 1) ? '<input type="radio" name="heater_5" value="1" checked/>On<input type="radio" name="heater_5" value="0"/>Off' : '<input type="radio" name="heater_5" value="1" />On<input type="radio" name="heater_5" value="0" checked/>Off') . '<div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div></div></td>
		<td></td>
	      </tr>';
	echo '</tbody>';
	echo '</table>';
	echo '<input type="hidden" name="rabbit_ip" value="' . $rabbit_ip . '"/>';
	echo '<input type="hidden" name="free_server_port" value="' . $free_server_port . '"/>';
	echo '<input type="hidden" name="auto_control" value="' . $auto_control . '"/>';
	echo '<input type="hidden" name="manual_fan" value="' . $manual_fan . '"/>';
	echo '<input type="hidden" name="manual_heater" value="' . $manual_heater . '"/>';
	echo '<input type="hidden" name="air_intake" value="' . $air_intake . '"/>';
	echo '<input type="hidden" name="gsn_id" value="' . $selected_gsn . '"/>';
	echo "<br/>Current recipients: <br/>";
	$first = true;
	$num = 0;
	if (!$emails) {
	    echo "There are no recipients for this managing system currently!<br/>";
	    echo '<br/><label style="margin: 0; padding: 0; border: 0; font-size: 100%; float:left; text-align:right; font-weight:bold;">Recipient ' . ($num+1) . ':&nbsp&nbsp</label><input style="width: 200px; margin: 0; padding: 0; font-size: 100%; text-align:left; float:left;" type="text" id="recipient_' . $num . '" name="recipient_' . $num . '" value="' . $recipient . '"/>';
	} else {
	    foreach ($emails as $recipient) {
		if ($first) {
		    echo '<br/><label style="margin: 0; padding: 0; border: 0; font-size: 100%; float:left; text-align:right; font-weight:bold;">Recipient ' . ($num+1) . ':&nbsp&nbsp</label><input style="width: 200px; margin: 0; padding: 0; font-size: 100%; text-align:left; float:left;" type="text" id="recipient_' . $num . '" name="recipient_' . $num . '" value="' . $recipient . '"/>';
		    $all_recipients = $recipient;
		    $first = false;
		} else {
		    echo '<br/><label style="margin: 0; padding: 0; border: 0; font-size: 100%; float:left; text-align:right; font-weight:bold;">Recipient ' . ($num+1) . ':&nbsp&nbsp</label><input style="width: 200px; margin: 0; padding: 0; font-size: 100%; text-align:left; float:left;" type="text" id="recipient_' . $num . '" name="recipient_' . $num . '" value="' . $recipient . '"/>';
		    $all_recipients .= '!!' . $recipient;
		}
		$num++;
	    }
	    echo "<br/><br/>";
	}

	$num--;
	echo '<input type="hidden" name="recipient_number" value="' . $num . '"/>';
	echo '<input type="hidden" name="recipients_emails" value="' . $all_recipients . '"/>';
	echo '<input type="button" id="refresh_table_button" name="refresh" value="Refresh"/>';
	echo CHtml::submitButton('Save changes');
	echo '</form>';
	echo '</div>';
	echo "</div>";
    }

    public function liveDataOutput($selected_gsn) {
	try {
	    $gsn = DiGsn::model()->findByPk($selected_gsn);

	    if (!empty($gsn)) {
		if ($gsn['port_ssl']) {
		    $GSN_url = "https://" . $gsn['username'] . ":" . $gsn['password'] . "@" . $gsn['gsn_ip'] . ":" . $gsn['port_ssl'];
		} else {
		    $GSN_url = "http://" . $gsn['username'] . ":" . $gsn['password'] . "@" . $gsn['gsn_ip'] . ":" . $gsn['gsn_port'];
		}

		$gsnHandler = $GSN_url . '/gsn';
		

		if (@fopen($gsnHandler, "rb")) {
//open xml file
		    $xml_file = simplexml_load_file($gsnHandler);

		    echo '<br/><div style="border-top-style:solid;">';
		    echo '<h3><b>Live data overview</b></h3><br/>';
		    echo "This is a list of currently active sensors that are taken into consideration when managing this system. If there are no sensors presented, please contact us as soon as possible as this can go on as a potential error.<br/><br/>";
		    foreach ($xml_file->children() as $virtual_sensor) {

			$str = substr($virtual_sensor['name'], 0, 2);

			/*
			 * by the aggrement only sensors with name a_ are considered to be improtant
			 * and are measuring numerical units
			 */
			if (strcmp($str, "a_") != 0) {
			    continue;
			}

//echo $virtual_sensor['name']."<br/>";

			$select_sensor = DiSensors::model()->find('sensor_name=:parent_id and gsn_id=:gsn_id', array(':gsn_id' => $selected_gsn, ':parent_id' => $virtual_sensor['name']));

			echo '<div class="">';

			if (!empty($select_sensor)) {
			    $time = "No measures occured";
			    $measured = "";
			    foreach ($virtual_sensor as $field) {
				if (strcmp($field['name'], "timed") == 0)
				    $time = $field;
				if (strcmp($field['category'], "predicate") == 0 || strcmp($field['name'], "timed") == 0) {
				    continue;
				} else {
				    $unit_help_array = array();
				    $unit_help_array = explode("_unit_", $field['name']);

				    /*
				     * depending of the situation, we have two cases
				     * in first case we know only unit name, and check only it (if unit is named normally)
				     * in second case we know both because of sensor name being in format name_unit_mark
				     */
				    $unit = $unit_help_array[0];

				    $measured .= '&nbsp&nbsp&nbsp' . $unit . ": " . $field . "<br/>";
				}
			    }

			    echo $select_sensor['sensor_user_name'] . ', last reading: ' . $time . '<br/>' . $measured;
			}

			echo "</div>";
		    }

		    echo "</div>";
		}
		else
		    echo "Unable to load XML file for live data readings.<br/>";
	    }
	} catch (Exception $ae) {
	    echo "Data is <b>NOT</b> available directly from GSN server! GSN could be down for maintenance, otherwise we are encountering some difficulties and we apologize for it! If the problem persists, feel free to contact our administrator for help!<br/>";
	}
    }

    public function gatherGsnConfigStats($selected_gsn, &$return_message
    , &$externalTempLimit
    , &$internalTempLimit1
    , &$internalTempLimit2
    , &$internalTempLimit3
    , &$fan_1, &$fan_2, &$fan_3, &$fan_4, &$fan_5
    , &$heater_1, &$heater_2, &$heater_3, &$heater_4, &$heater_5
    , &$rabbit_ip
    , &$free_server_port
//state concerning variables
    , &$auto_control
    , &$manual_fan
    , &$manual_heater
    , &$air_intake
    , &$emails) {
	$gsn = DiGsn::model()->findByPk($selected_gsn);

	if (!empty($gsn)) {
	    if ($gsn['port_ssl']) {
		$GSN_url = "https://" . $gsn['username'] . ":" . $gsn['password'] . "@" . $gsn['gsn_ip'] . ":" . $gsn['port_ssl'];
	    } else {
		$GSN_url = "http://" . $gsn['username'] . ":" . $gsn['password'] . "@" . $gsn['gsn_ip'] . ":" . $gsn['gsn_port'];
	    }

	    $controlStatus = $GSN_url . '/passiveheating/config';

//echo $controlStatus."<br/>";
//echo $controlStatus;
//ovo se koristi samo za testiranje, obavezno promijeniti!!!!!
//$controlStatus = 'http://161.53.67.224/testenv/config.xml';

	    try {

		if (@fopen($controlStatus, "r")) {
//open xml file
		    $xml_config = simplexml_load_file($controlStatus);

		    foreach ($xml_config->children() as $information) {
			if (strcmp($information->getName(), 'state') == 0) {
			    foreach ($information->children() as $current_status) {
				if (strcmp($current_status->getName(), 'auto-control') == 0)
				    $auto_control = $current_status;
				if (strcmp($current_status->getName(), 'manual-fan') == 0)
				    $manual_fan = $current_status;
				if (strcmp($current_status->getName(), 'manual-heater') == 0)
				    $manual_heater = $current_status;
				if (strcmp($current_status->getName(), 'air-intake') == 0)
				    $air_intake = $current_status;
			    }
			} else if ((strcmp($information->getName(), 'core-parameters') == 0)) {
			    foreach ($information->children() as $core) {
//temperature values that are used in our algorithm
				if (strcmp($core->getName(), 'externalTempLimit') == 0)
				    $externalTempLimit = $core;
				if (strcmp($core->getName(), 'internalTempLimit1') == 0)
				    $internalTempLimit1 = $core;
				if (strcmp($core->getName(), 'internalTempLimit2') == 0)
				    $internalTempLimit2 = $core;
				if (strcmp($core->getName(), 'internalTempLimit3') == 0)
				    $internalTempLimit3 = $core;

//rabbit ip
				if (strcmp($core->getName(), 'rabbit-ip') == 0)
				    $rabbit_ip = $core;

				if (strcmp($core->getName(), 'free-server-port') == 0)
				    $free_server_port = $core;

//state 1
				if (strcmp($core->getName(), 'state1') == 0) {
				    foreach ($core->children() as $state) {
					if (strcmp($state->getName(), 'fan') == 0)
					    $fan_1 = $state;
					if (strcmp($state->getName(), 'heater') == 0)
					    $heater_1 = $state;
				    }
				}

//state2
				if (strcmp($core->getName(), 'state2') == 0) {
				    foreach ($core->children() as $state) {
					if (strcmp($state->getName(), 'fan') == 0)
					    $fan_2 = $state;
					if (strcmp($state->getName(), 'heater') == 0)
					    $heater_2 = $state;
				    }
				}

//state3
				if (strcmp($core->getName(), 'state3') == 0) {
				    foreach ($core->children() as $state) {
					if (strcmp($state->getName(), 'fan') == 0)
					    $fan_3 = $state;
					if (strcmp($state->getName(), 'heater') == 0)
					    $heater_3 = $state;
				    }
				}

//state4
				if (strcmp($core->getName(), 'state4') == 0) {
				    foreach ($core->children() as $state) {
					if (strcmp($state->getName(), 'fan') == 0)
					    $fan_4 = $state;
					if (strcmp($state->getName(), 'heater') == 0)
					    $heater_4 = $state;
				    }
				}

//state5
				if (strcmp($core->getName(), 'state5') == 0) {
				    foreach ($core->children() as $state) {
					if (strcmp($state->getName(), 'fan') == 0)
					    $fan_5 = $state;
					if (strcmp($state->getName(), 'heater') == 0)
					    $heater_5 = $state;
				    }
				}
			    }
			} else if ((strcmp($information->getName(), 'notifications') == 0)) {
			    $emails = array();
			    foreach ($information->children() as $email) {
//temperature values that are used in our algorithm
				if (strcmp($email->getName(), 'email') == 0)
				    array_push($emails, $email);
			    }
			}
		    }

		    if (!isset($emails)) {
			$emails = false;
		    }

		    if (!isset($air_intake)) {
//Air intake system does not exist on this system
			$air_intake = false;
		    }

		    if (!isset($externalTempLimit)
			    || !isset($auto_control)
			    || !isset($manual_fan)
			    || !isset($manual_heater)
			    || !isset($internalTempLimit1)
			    || !isset($internalTempLimit2)
			    || !isset($internalTempLimit3)
			    || !isset($rabbit_ip)
			    || !isset($free_server_port)
			    || !isset($fan_1) || !isset($fan_2) || !isset($fan_3) || !isset($fan_4) || !isset($fan_5)
			    || !isset($heater_1) || !isset($heater_2) || !isset($heater_3) || !isset($heater_4) || !isset($heater_5)
			    || !isset($emails)
		    ) {
//error occured or XML was invalid
			$return_message = "Unable to review config file on GSN server as expected. Please contact administrator for further information.</br>";
			return false;
		    } else {
//$model->configTableOutput($externalTempLimit, $internalTempLimit1, $internalTempLimit2, $internalTempLimit3, $fan_1, $fan_2, $fan_3, $fan_4, $fan_5, $heater_1, $heater_2, $heater_3, $heater_4, $heater_5, $rabbit_ip, $free_server_port, $auto_control, $manual_fan, $manual_heater, $emails, $selected_gsn);
			$return_message = "Everything went ok!";
			return true;
		    }
		} else {
		    $return_message = "On this GSN server, heating managment system is not supported. Please contact us for further details. We apologize for the inconvinience.</br>";
		    return false;
		}
	    } catch (Exception $e) {
		$return_message = "ERROR occured: Unable to open XML!<br/>";
		return false;
	    }
	} else {
	    $return_message = "<b>ERROR: </b>This GSN appears to be invalid! Please try again or contact our administrator. We apologize for the inconvinience!<br/>";
	    return false;
	}
	$return_message = "Somehow an error went unnoticed and system failed to return valid information! Contact administrator for further support!<br/>";
	return false;
    }

}

?>
