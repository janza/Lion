<?php

function extract_readings($GSN_CODE, &$mail_body, &$error_occured) {

    $error_message = "";
    $unknown_sensors = "";
    $known_sensors = "";
    $end_time = "";
    //$error_occured = false;
    $file = true;

    try {
	include_once "GSN_database_connection.php";
	include "database_connection.php";

	if ($database_connection_status) {
	    $help_date = 0;

	    $GSN_name_conn = GSN_DB_connection($GSN_CODE);
		if (!$GSN_name_conn)
			return false;
		
	    $dbconnGSN = $GSN_name_conn[0];
	    $GSN_name = $GSN_name_conn[1];

	    //open log file
	    $file_name = $GSN_name . "_ETLlog.txt";
	    try {
			$logfile = fopen($file_name, 'a');
			$file = true;
	    } catch (Exception $ae) {
			$error_occured = true;
			$file = false;
			$error_message = "Error occured while opening the file " . $file_name;
	    }

	    $date_time = date('Y-m-d H:i:s');
	    if ($file) {
		fwrite($logfile, "$date_time");
		fwrite($logfile, " --> extracting data started \n");
	    }

	    $start_time = "$date_time --> extracting data started \n";

	    //first we need to find out when the last reading has occured for each sensor on this GSN
	    $last_readings = pg_prepare($dbconn, "lr", "select e.time_of_reading FROM etl_log e WHERE e.gsn_id = $1 AND e.sensor_id = $2");

	    //select all table names from GSN's database
	    $table_list = pg_query($dbconnGSN, "select c.relname FROM pg_catalog.pg_class c
							LEFT JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
							WHERE c.relkind IN ('r','') AND n.nspname NOT IN ('pg_catalog', 'pg_toast')
							AND pg_catalog.pg_table_is_visible(c.oid)");
	    if (!$table_list) {
		$error_occured = true;
		if ($file) {
		    fwrite($logfile, "An error occured while selecting all tables \r\n\r\n");
		}
		$error_message .="An error occured while selecting all tables" . pg_last_error() . "\r\n\r\n";

		$mail_body .= $start_time . "ERROR occured:\r\n\r\n" . $error_message . "ETL_process encountered error during fetching tables.\r\n";
		return false;
	    }

	    //prepare statement for selecting sensor code and sensor type
	    $select_stmt = pg_prepare($dbconn, "ss", "SELECT sensor_id, sensor_type from di_sensors WHERE sensor_name = $1 and gsn_id = $2 and is_active='1'");

	    //prepare statement for inserting new reading in l_readings
	    $insert_stmt = pg_prepare($dbconn, "is", "INSERT INTO l_readings (gsn_id, sensor_id, unit_id, date_id, time_id, time_of_the_reading, value ) VALUES ($1, $2, $3, $4, $5, $6, $7)");

	    //find dateID
	    $dateID_stmt = pg_prepare($dbconn, "ds", "SELECT date_id FROM di_days WHERE date = $1");

	    //find timeID
	    $timeID_stmt = pg_prepare($dbconn, "ts", "SELECT time_id FROM di_time WHERE time = $1");
	    //$etl_prepare = pg_prepare ($dbconn, "el", "update etl_log e set time_of_reading = (select max(time_of_the_reading) as time_of_reading from l_readings l where l.sensor_id = e.gsn_id and l.gsn_id = e.gsn_id group by l.sensor_id, l.gsn_id)");
	    //prepare check statement, check statement is used in order to determine if sensor is already saved inside of warehouse
	    $check_stmt = pg_prepare($dbconn, "cs", "SELECT sensor_id from di_sensors WHERE sensor_name = $1 and gsn_id = $2 and is_active='1'");

	    //go through all tables (all sensors)
	    while ($table_array = pg_fetch_row($table_list)) {

		$sensor = pg_execute($dbconn, "ss", array($table_array[0], $GSN_CODE));
		if (!$sensor) {
		    $error_occured = true;
		    $error_message .= "Error in SQL query when fetching sensor_id from sensor  \"" . $table_array[0] . "\" " . pg_last_error() . "\r\n";
		    if ($file) {
			fwrite($logfile, "Error in SQL query when fetching sensor_id from sensor  \"" . $table_array[0] . "\" " . pg_last_error() . "\r\n");
		    }
		    continue;
		}

		$abc = pg_num_rows($sensor);
		//if there is sensor with this name than
		if ($abc > 0) {

		    $sensor_row = pg_fetch_row($sensor);

		    $last_reading_exec = pg_execute($dbconn, "lr", array($GSN_CODE, $sensor_row[0]));
		    $last_reading_datetime = pg_fetch_row($last_reading_exec);
		    $last_reading_timestamp = strtotime($last_reading_datetime[0]);

		    $known_sensors .= "Last reading for sensor name = \"$table_array[0]\" occured on " . $last_reading_timestamp . ", which stands for: " . $last_reading_datetime[0] . "\r\n";
		    if ($file) {
			fwrite($logfile, "Last reading for sensor name = \"$table_array[0]\" occured on " . $last_reading_timestamp . ", which stands for: " . $last_reading_datetime[0] . "\r\n");
		    }

		    //+1 is added because of the miliseconds. if we miss this, we will double data because reading was done couple of microseconds after the time we put in the database
		    $select_query = "SELECT * FROM " . $table_array[0] . " WHERE timed > " . ($last_reading_timestamp + 1) * 1000 . " ORDER BY timed";

		    //select all readings from table
		    $readings = pg_query($dbconnGSN, $select_query);

		    //see how many columns in table
		    $num_of_columns = pg_num_fields($readings);

		    /*
		     * we need to store what this sensor measured according to unit
		     * also, we need to take care of the cases when unit name (column name)
		     * is in new format (name_unit_mark)
		     */
		    for ($i = 2; $i < $num_of_columns; $i++) {
			$unit_name = pg_field_name($readings, $i);

			$unit_help_array = array();
			$unit_help_array = explode("_unit_", $unit_name);

			/*
			 * depending of the situation, we have two cases
			 * in first case we know only unit name, and check only it (if unit is named normally)
			 * in second case we know both because of sensor name being in format name_unit_mark
			 */
			if (count($unit_help_array) == 1)
			    $query = "SELECT unit_id from di_units WHERE unit_name = '$unit_help_array[0]' and etl_unit = 'unknown'";
			else
			    $query = "SELECT unit_id from di_units WHERE unit_name = '$unit_help_array[0]' AND etl_unit = '$unit_help_array[1]'";

			$result = pg_query($dbconn, $query);

			if (!$result) {
			    $error_occured = true;
			    $error_message .= "Error in SQL query when fetching unitCode for unitName = \"$unit_name\"  and sensor name = \"$table_array[0]\" ....   " . pg_last_error() . "\r\n\r\n";
			    if ($file) {
				fwrite($logfile, "Error in SQL query when fetching unitCode for unitName = \"$unit_name\"  and sensor name = \"$table_array[0]\" ....   " . pg_last_error() . "\r\n");
			    }
			    /* JOS PROVJERITI DUMMY ZAPIS ZA SVAKI SLUCAJ! */
			    $unit_array[$i] = -1;
			} else {
			    if (pg_num_rows($result) == 0)
				$unit_array[$i] = -1;
			    else {
				$unit_code = pg_fetch_row($result);
				$unit_array[$i] = $unit_code[0];
			    }
			}
		    }

		    //for every reading
		    while ($readings_row = pg_fetch_row($readings)) {
			for ($i = 2; $i < $num_of_columns; $i++) {

			    $timestamp = date('Y-m-d H:i:s', $readings_row[1] / 1000);
			    $date_time = explode(" ", $timestamp);

			    //fetch dateID from di_days but only if it is not equal to previous
			    if (!($help_date == $date_time[0])) {

				$res_date = pg_execute($dbconn, "ds", array($date_time[0]));
				if (!$res_date) {
				    $error_occured = true;
				    $error_message .= "Error in SQL query when fetching dateID   " . pg_last_error() . "\r\n\r\n";
				    if ($file) {
					fwrite($logfile, "Error in SQL query when fetching dateID   " . pg_last_error() . "\r\n");
				    }
				    $dateID = '19000101';
				} else {
				    $res_date = pg_fetch_row($res_date);
				    $dateID = $res_date[0];
				}
				$help_date = $date_time[0];
			    }
			    //fetch timeID from di_time
			    $res_time = pg_execute($dbconn, "ts", array($date_time[1]));
			    if (!$res_time) {
				$error_occured = true;
				$error_message .= "Error in SQL query when fetching timeID   " . pg_last_error() . "\r\n\r\n";
				if ($file) {
				    fwrite($logfile, "Error in SQL query when fetching timeID   " . pg_last_error() . "\r\n");
				}
				/* !!JOS PROVJERITI KOJI JE DUMMY */
				$timeID = '0';
			    } else {
				$res_time = pg_fetch_row($res_time);
				$timeID = $res_time[0];
			    }

			    //insert into l_readings
			    $result = pg_execute($dbconn, "is", array($GSN_CODE, $sensor_row[0], $unit_array[$i], $dateID, $timeID, $timestamp, $readings_row[$i]));

			    if (!$result) {
				$error_occured = true;
				$error_message .= "Error in SQL query when inserting into l_readings info about sensor with name \"" . $table_array[0] . "\"  " . pg_last_error() . "\r\n\r\n";
				if ($file) {
				    fwrite($logfile, "Error in SQL query when inserting into l_readings info about sensor with name \"" . $table_array[0] . "\"  " . pg_last_error() . "\r\n");
				}
			    }
			}
		    }
		} else {
		    $unknown_sensors .= "Sensor with name \"" . $table_array[0] . "\" was not included.\r\n";
		    if ($file) {
			fwrite($logfile, "Sensor with name \"" . $table_array[0] . "\" was not included.\r\n");
		    }
		}
	    }

	    $result = pg_query($dbconn, "UPDATE etl_log e set time_of_reading = (select max(time_of_the_reading) as time_of_reading from l_readings l where l.sensor_id = e.sensor_id and l.gsn_id = e.gsn_id group by l.sensor_id, l.gsn_id)");

	    if (!$result) {
		$error_message.="ERROR occured during updating etl_log from l_readings! Last error: " . pg_last_error() . '\r\n';
		if ($file) {
		    fwrite($logfile, "ERROR occured during updating etl_log from l_readings! Last error: " . pg_last_error() . '\r\n');
		}
		$error_occured = true;
	    }

	    $result = pg_query($dbconn, "UPDATE etl_log e set time_of_reading = (select max(time_of_the_reading) as time_of_reading from f_readings l where l.sensor_id = e.sensor_id and l.gsn_id = e.gsn_id group by l.sensor_id, l.gsn_id) where e.time_of_reading is null");

	    if (!$result) {
		$error_message.="ERROR occured during updating etl_log from f_readings! Last error: " . pg_last_error() . '\r\n';
		if ($file) {
		    fwrite($logfile, "ERROR occured during updating etl_log from f_readings! Last error: " . pg_last_error() . '\r\n');
		}
		$error_occured = true;
	    }

	    $result = pg_query($dbconn, "UPDATE etl_log e set time_of_reading = '1.1.1900 0:00' where time_of_reading is null");

	    if (!$result) {
		$error_message.="ERROR occured during updating etl_log for null values! Last error: " . pg_last_error() . '\r\n';
		if ($file) {
		    fwrite($logfile, "ERROR occured during updating etl_log for null values! Last error: " . pg_last_error() . '\r\n');
		}
		$error_occured = true;
	    }

	    $date_time = date('Y-m-d H:i:s');
	    if ($file) {
		fwrite($logfile, "$date_time");
		fwrite($logfile, " --> extracting data ended \r\n\r\n");
		fclose($logfile);
	    }

	    $end_time = "$date_time --> extracting data ended \r\n";

	    pg_close($dbconnGSN);

	    if ($error_occured) {
		$mail_body .= $start_time . "ERROR occured:\r\n\r\n" . $error_message . "\r\nKnown sensors:\r\n" . $known_sensors . "\r\nUnknown sensors:\r\n" . $unknown_sensors ."\r\n". $end_time;
		return false;
	    } else {
		$mail_body .= $start_time . "\r\nKnown sensors:\r\n" . $known_sensors . "\r\nUnknown sensors:\r\n" . $unknown_sensors . "\r\n".$end_time;
		return true;
	    }
	} else {
	    $error_occured = true;
	    $error_message = "Unable to connect to local database in ETL_process!\n\r";
	    $mail_body .= $start_time . "ERROR occured:\r\n\r\n" . $error_message . "\r\nKnown sensors:\r\n" . $known_sensors . "\r\nUnknown sensors:\r\n" . $unknown_sensors . $end_time;

	    return false;
	}
    } catch (Exception $ae) {
	$error_occured = true;
	$error_message .= "Error occured during ETL_process.php execution. Please check the details!\n\r";
	$mail_body .= $start_time . "ERROR occured:\r\n\r\n" . $error_message . "\r\nKnown sensors:\r\n" . $known_sensors . "\r\nUnknown sensors:\r\n" . $unknown_sensors . $end_time;

	return false;
    }
}
?>


