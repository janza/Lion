<?php

/**
 * Function for storing info about sensors in the database
 * @param $GSN_ID 
 */
function add_sensor($GSN_ID, &$message_body, &$error_occured, &$error_message) {

    //connect to database
    include "database_connection.php";

    $active_sensors = array();
    $file = true;
    //initalization
    array_push($active_sensors, "dummy");

    if ($database_connection_status) {

	try {
	    $GSN_url_name_array = build_url($dbconn, $GSN_ID, &$message_body);
	    $GSN_url = $GSN_url_name_array[0];
	    $GSN_name = $GSN_url_name_array[1];

	    if  (@fopen($GSN_url, "r")) {
		//open xml file
		$xml_file = simplexml_load_file($GSN_url);
		$message_body .= "Successful XML loading on " . $GSN_url . "\n";
	    } else {
		$error_message .= "Could not locate XML on " . $GSN_url . "\n";
		$error_occured =true;
		return false;
	    }

	    //open log file
	    $file_name = $GSN_name . "_addSensorLog.txt";

	    try {
		$logfile = fopen($file_name, 'a');
		$file = true;
	    } catch (Exception $ae) {
		$error_occured = true;
		$error_message .= "Problem occured while opening " . $file_name . " ERROR: " . $ae->getMessage();
		$file = false;
	    }

	    $date_time = date('Y-m-d H:i:s');
	    if ($file) {
		fwrite($logfile, "$date_time");
		fwrite($logfile, " --> adding sensors started \r\n");
	    }
	    $message_body .= "$date_time ---> adding sensors started \n";

	    //get units name and units code into array
	    $units = get_all_units($dbconn, &$message_body);
	    if (!$units) {
					    $error_occured = true;
		$error_message .= "Problem while loading info about units!!!\r\n";
		if ($file) {
		    fwrite($logfile, "Problem while loading info about units!!! \r\n");
		}
		return false;
	    }

	    //prepare check statement, check statement is used in order to determine if sensor is already saved inside of warehouse
	    $check_stmt = pg_prepare($dbconn, "cs", "SELECT sensor_id from di_sensors WHERE sensor_name = $1 and gsn_id = $2");

	    //prepare update statment
	    $update_stmt = pg_prepare($dbconn, "us", "UPDATE di_sensors SET sensor_type = $1, location_x = $2, location_y = $3, date_activated_id = $4,
											  date_deactivated_id = $5, is_active = $6, is_dummy = $7, is_real_sensor = $8
											  WHERE sensor_id= $9");

	    //prepare INSERT INTO di_sensors statment
	    $stmt = pg_prepare($dbconn, "ps", "INSERT INTO di_sensors (sensor_name, sensor_user_name,
								gsn_id, sensor_type, location_x, location_y, date_activated_id, 
								date_deactivated_id, is_active, is_dummy, is_real_sensor) 
								VALUES($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11)");
	    //find dateID
	    $dateID_stmt = pg_prepare($dbconn, "ds", "SELECT date_id FROM di_days WHERE date = $1");

	    //statement for selecting sensor names, to determin if any is inactive
	    $checkInActive_stmt = pg_prepare($dbconn, "cIAs", "SELECT sensor_name FROM di_sensors WHERE gsn_id = $1 and is_active='1'");

	    //statement for setting sensor data when deactivated
	    $deactivate_stmt = pg_prepare($dbconn, "deacs", "UPDATE di_sensors SET date_deactivated_id = $1, is_active = '0' WHERE sensor_name = $2 AND gsn_id = $3");

	    //statement for inserting into f_sensor_type
	    $sensor_unit = pg_prepare($dbconn, "sus", "INSERT into f_sensor_type (unit_id, sensor_id) VALUES ($1, $2)");

	    //statement for selecting
	    $select_f_sensor_type = pg_prepare($dbconn, "sfst", "SELECT unit_id, sensor_id FROM f_sensor_type WHERE sensor_id = $1 AND unit_id = $2");

	    $etl_prepare = pg_prepare($dbconn, "el", "INSERT into etl_log VALUES ($1, $2, $3)");

	    //statement for finding appropriate unit
	    $unit_stmt_name = pg_prepare($dbconn, "unit_name", "SELECT unit_id, unit_name FROM di_units u WHERE u.unit_name = $1");
	    //statement for finding appropriate unit
	    $unit_stmt_all = pg_prepare($dbconn, "unit_all", "SELECT unit_id, unit_name FROM di_units u WHERE u.unit_name = $1 and u.etl_unit = $2");

	    //statement for inserting new unit
	    $unit_ins = pg_prepare($dbconn, "ins_unit", "INSERT INTO di_units (unit_name, unit_mark, etl_unit) VALUES ($1,$2,$3)");

	    $double_check = pg_prepare($dbconn, "dc_etl", "SELECT * from etl_log where gsn_id = $1 and sensor_id = $2");

	    //help varibles
	    //$i=1;
	    $null = null;
	    $active = '1';
	    $dummy = '0';
	    $real_sensor = '1';

	    //go through every element of xml file
	    foreach ($xml_file->children() as $virtual_sensor) {

		$str = substr($virtual_sensor['name'], 0, 2);

		/*
		 * by the aggrement only sensors with name a_ are considered to be improtant
		 * and are measuring numerical units
		 */
		if (strcmp($str, "a_") != 0) {
		    continue;
		}

		$sensorUserName = substr($virtual_sensor['name'], 2);

		$location_y = null;
		$location_x = null;

		//get date of activation
		$number = (double) $virtual_sensor['last-modified'];
		$date_activated = date('Y-m-d H:i:s', $number / 1000);
		$date = explode(" ", $date_activated);

		$res_date = pg_execute($dbconn, "ds", array($date[0]));
		if (!$res_date) {
		    			    $error_occured = true;
		    $error_message .= ( "Error in SQL query when fetching dateID   " . pg_last_error() . "\r\n");
		    if ($file) {
			fwrite($logfile, "Error in SQL query when fetching dateID   " . pg_last_error() . "\r\n");
		    }
		    $date_activated_id = '19000101';
		} else {
		    $res_date = pg_fetch_row($res_date);
		    $date_activated_id = $res_date[0];
		}

		$sensor_type = "";
		$unit_array = array();
		//go through every child
		foreach ($virtual_sensor->children() as $field) {
		    //first we check if this child shows geographical data
		    switch ($field['name']) {
			case "latitude" :
			    $location_y = $field;
			    break;
			case "longitude" :
			    $location_x = $field;
			    break;
		    }
		    //second is sensor type
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
			if (strcmp($sensor_type, "") == 0)
			    $sensor_type .= $unit_help_array[0];
			else
			    $sensor_type .= ", " . $unit_help_array[0];

			if (count($unit_help_array) == 1)
			    $check_unit = pg_execute($dbconn, "unit_all", array(strtolower($unit_help_array[0]), "unknown"));
			else
			    $check_unit = pg_execute($dbconn, "unit_all", array(strtolower($unit_help_array[0]), $unit_help_array[1]));

			if (!$check_unit) {
			    			    $error_occured = true;
			    $error_message .= "ERROR occured while trying to fetch " . $unit_help_array[0] . " with unit_all for sensor" . $sensorUserName . "\r\n";
			    continue;
			} else {
			    if (pg_num_rows($check_unit) != 0) {
				$unit_data = pg_fetch_row($check_unit);

				array_push($unit_array, $unit_data[0]);
				//$message_body .= $sensorUserName . " has unit " . $unit_help_array[0] . " with known ID: " . $unit_data[0] . "\r\n";
			    } else {
				/*
				 * if we do not have unit in di_units we need to insert it and fill in the sensor_type
				 * as well as our array of units
				 */
				try {
				    if (count($unit_help_array) == 1) {
					$ins_new_unit = pg_execute($dbconn, "ins_unit", array(trim(strtolower($unit_help_array[0])), trim("unknown"), trim("unknown")));
					$unit_inserted = pg_execute($dbconn, "unit_all", array(trim(strtolower($unit_help_array[0])), "unknown"));
				    } else {
					$ins_new_unit = pg_execute($dbconn, "ins_unit", array(trim(strtolower($unit_help_array[0])), trim($unit_help_array[1]), trim($unit_help_array[1])));
					$unit_inserted = pg_execute($dbconn, "unit_all", array(trim(strtolower($unit_help_array[0])), trim($unit_help_array[1])));
				    }
				} catch (Exception $ae) {
				    			    $error_occured = true;
				    $error_message .= "ERROR occured while trying to insert new unit in the database or fetch its value! " . $ae->getMessage();
				}

				if (!$ins_new_unit || !$unit_inserted) {
				    //we did not successfuly insert unit or we could not find one
				    			    $error_occured = true;
				    $error_message .= "ERROR occured while trying to insert new unit in the database or fetch its value! " . pg_last_error() . "\r\n";
				} else {
				    $unit_data = pg_fetch_row($unit_inserted);
				    array_push($unit_array, $unit_data[0]);
				    $message_body .= $sensorUserName . " has unit that was not in the system before " . $unit_help_array[0] . " with NEW unit_id: " . $unit_data[0] . "\r\n";
				}
			    }
			}
		    }
		}

		//save names of active sensors,
		array_push($active_sensors, $virtual_sensor['name']);

		//check if there is already this sensor in database
		$check_result = pg_execute($dbconn, "cs", array($virtual_sensor['name'], $GSN_ID));
		if (!$check_result) {
		    			    $error_occured = true;
		    $error_message .= ( "Error in SQL query when checking if sensor \"" . $virtual_sensor['name'] . "\" exists in warehouse" . pg_last_error() . "\r\n");
		    if ($file) {
			fwrite($logfile, "Error in SQL query when checking if sensor \"" . $virtual_sensor['name'] . "\" exists in warehouse" . pg_last_error() . "\r\n");
		    }
		} else {
		    $check_result = pg_fetch_row($check_result);

		    //if sensor is already in the database update it
		    if (!empty($check_result)) {

			$update_result = pg_execute($dbconn, "us", array($sensor_type, $location_x, $location_y, $date_activated_id, $null,
				    $active, $dummy, $real_sensor, $check_result[0]));

			if (!$update_result) {
			    			    $error_occured = true;
			    $error_message .= ( "Error in SQL query when updating sensor with name \"" . $virtual_sensor['name'] . "\"  " . pg_last_error() . "\r\n");
			    if ($file) {
				fwrite($logfile, "Error in SQL query when updating sensor with name \"" . $virtual_sensor['name'] . "\"  " . pg_last_error() . "\r\n");
			    }
			}
			//if not than insert this sensor in database
		    } else {
			//fill prepared statement with data, and execute
			$result = pg_execute($dbconn, "ps", array($virtual_sensor['name'], $sensorUserName, $GSN_ID, $sensor_type,
				    $location_x, $location_y, $date_activated_id, $null, $active, $dummy, $real_sensor));

			if (!$result) {
			    			    $error_occured = true;
			    $error_message .= ( "Error in SQL query: INSERT INTO di_sensors " . pg_last_error() . "\r\n");
			    if ($file) {
				fwrite($logfile, "Error in SQL query: INSERT INTO di_sensors " . pg_last_error() . "\r\n");
			    }
			}
		    }

		    //get sensor code (sensor_id column) in order to store information about sensor type in f_sensor_type
		    $sensor_code = pg_execute($dbconn, "cs", array($virtual_sensor['name'], $GSN_ID));

		    if (!$sensor_code) {
			$error_occured = true;
			$error_message .= "ERROR occured while fetching sensor_id for sensor name " . $virtual_sensor['name'] . "\r\n";
		    } else {
			$sensor_code = pg_fetch_row($sensor_code);

			$double_check = pg_execute($dbconn, "dc_etl", array($GSN_ID, $sensor_code[0]));

			if (!$double_check) {
			    			    $error_occured = true;
			    $error_message .= "ERROR occured while checking for etl_log information, sensor_id = " . $sensor_code[0] . "!\r\n";
			} else {
			    if (pg_num_rows($double_check) == 0) {
				//fill prepared statement with data, and execute
				$etl_log = pg_execute($dbconn, "el", array($GSN_ID, $sensor_code[0], date("Y-m-d h:i", mktime(0, 0, 0, 1, 1, 2000))));

				if (!$etl_log) {
				    			    $error_occured = true;
				    $error_message .= ( "Error in SQL query: Insert into etl_log " . pg_last_error() . "\r\n");
				    if ($file) {
					fwrite($logfile, "Error in SQL query: Insert into etl_log " . pg_last_error() . "\r\n");
				    }
				} else {
				    $message_body .= "Sensor " . $sensorUserName . "with sensor_id=" . $sensor_code[0] . " has been successfully inserted in etl_log table!\r\n";
				}
			    }
			}

			foreach ($unit_array as $unit_id) {
			    $unit_check = pg_execute($dbconn, "sfst", array($sensor_code[0], $unit_id));

			    if (!$unit_check) {
							    $error_occured = true;
				$error_message .= "ERROR occured while trying to find unit " . $unit_id . " to appropriate sensor " . $sensor_code[0];
				continue;
			    }

			    $unit_information = pg_fetch_row($unit_check);
			    if (empty($unit_information)) {
				/*
				 * if unit was not in the f_sensor_type table we need to insert it
				 */
				//$message_body .= "Does not exist Unit id: " . $unit_id . "Sensor_id " . $sensor_code[0] . "\r\n";
				$sensor_type_insert = pg_execute($dbconn, "sus", array($unit_id, $sensor_code[0]));
				$message_body .= "New unit was introduced for sensor " . $sensor_code[0] . ", name: " . $virtual_sensor['name'] . ", unit_id = " . $unit_id . "\n\r";
			    }
			    else{
				//$message_body .= "Unit id: " . $unit_id . "Sensor_id " . $sensor_code[0] . "\r\n";
			    }
			}
		    }
		}
	    }

	    //get all sensors in warehouse
	    $result = pg_execute($dbconn, "cIAs", array($GSN_ID));
	    if (!$result) {
					    $error_occured = true;
		$error_message .= ( "Error in SQL query: SELECT sensor_name from di_sensors  " . pg_last_error() . "\r\n");
		if ($file) {
		    fwrite($logfile, "Error in SQL query: SELECT sensor_name from di_sensors  " . pg_last_error() . "\r\n");
		}
	    } else {
		while ($active_sensor_name = pg_fetch_row($result)) {
		    $isActive = array_search($active_sensor_name[0], $active_sensors);

		    //if is inActive
		    if (!$isActive) {
			$message_body .= "sensor  ---  $active_sensor_name[0]--- inactive\r\n";
			$today = date('Y-m-d');
			//get dateID from di_days
			$dateID = pg_execute($dbconn, "ds", array($today));
			if (!$dateID) {
			    $error_occured = true;
			    $error_message .= ( "Error in SQL query when fetching dateID   " . pg_last_error() . "\r\n");
			    fwrite($logfile, "Error in SQL query when fetching dateID   " . pg_last_error() . "\r\n");
			}
			$dateID = pg_fetch_row($dateID);
			//update di_sensors, set sensor inactive
			$update_res = pg_execute($dbconn, "deacs", array($dateID[0], $active_sensor_name[0], $GSN_ID));
			if (!$update_res) {
			    $error_occured = true;
			    $error_message .= ( "Error in SQL query updating inActive sensor:   " . pg_last_error() . "\r\n");
			    fwrite($logfile, "Error in SQL query updating inActive sensor:   " . pg_last_error() . "\r\n");
			}
		    }
		}
	    }

	    $date_time = date('Y-m-d H:i:s');
	    $message_body .= ( "$date_time --> adding sensors ended \r\n\r\n");
	    if ($file) {
		fwrite($logfile, "$date_time");

		fwrite($logfile, " --> adding sensors ended \r\n\r\n\r\n");
		fclose($logfile);
	    }
	    pg_close($dbconn);

	    return true;
	} catch (Exception $ae) {
	    $error_occured = true;
	    $error_message .= "Unexpected error occured while adding sensors!\r\nERROR: " . $ae->getMessage();
		    return false;
	}
    } else {
	$error_occured = true;
	$error_message = "Unable to connect to the local database while adding_sensors\r\n";
	return false;
    }
}

/**
 * Function build_url builds URL for getting information about sensors on a certain GSN
 * @param $a first argument that is Database connection
 * @param $b second argument that is GSN code
 * @param $c third argument that is logfile
 * @return URL of GSN
 */
function build_url($a, $b, &$message_body) {

    $response = array();
    $query = "SELECT gsn_url, username, password, gsn_ip, port_ssl , gsn_port, gsn_name from di_gsn WHERE gsn_id = $b";
    $result = pg_query($a, $query);
    if (!$result) {
	$message_body .= ( "ERROR when selecting info for building url!..." . pg_last_error() . "\r\n");
	//fwrite($c, "ERROR when selecting info for building url!..." . pg_last_error() . "\r\n");
	$message_body .= "\n\r";
    }
    $gsn_info = pg_fetch_row($result);

    //check if there is information about SSL port, if yes than build https url, if not use ordinary http url
    if ($gsn_info[4]) {
	$url = "https://" . $gsn_info[1] . ":" . $gsn_info[2] . "@" . $gsn_info[3] . ":" . $gsn_info[4] . "/gsn";
    } else {
	$url = "http://" . $gsn_info[1] . ":" . $gsn_info[2] . "@" . $gsn_info[3] . ":" . $gsn_info[5] . "/gsn";
    }
    array_push($response, $url);
    array_push($response, $gsn_info[6]);
    return $response;
}

/**
 * Function for getting all units that are being measured by system
 * @param $a first argument that is Database connection
 * @return array that contains name of the unit and it's code in database
 */
function get_all_units($a, &$error_message) {

    $query = "SELECT unit_name, unit_id FROM di_units";
    $result = pg_query($a, $query);
    if (!$result) {
	$error_message .= ( "ERROR when selecting info about units..." . pg_last_error() . "\r\n");
	return false;
    }
    while ($unit_info = pg_fetch_row($result)) {
	$unit_array[strtolower($unit_info[0])] = $unit_info[1];
    }

    return $unit_array;
}
?>




