<?php

include "database_connection.php";

$message = "";
$subject = "Cron_aggregation";
//this email is reponsible for managing ETL process
try{
$data = fopen("/home/lpostruzin/cron_new/administratorInformation.txt","r");
$to = "";
	
while (($row = fgets($data)) != false){
	if ($to=="")
		$to = $row;
	else $to .= ", ". $row;
}
}
catch (Exception $ae)
{
	$to = "Unknown, ERROR: ".$ae->getMessage();
}

$message = "Administrator emails: ".$to."\n";
$from = "ColdWatch ETL administration";
$headers = "From:" . $from;

$error_status = "";
$error_occured = false;
try {
//open log file
    $file_name = "Aggregation_log.txt";

    if ($database_connection_status) {
	try {
	    $logfile = fopen($file_name, 'a');
	    $file = true;
	} catch (Exception $ae) {
	    $error_occured = true;
	    $error_status = "LOG_FILE";
	    $message .= "Unable to open Aggregation_log.txt file!\n\r";
	    $file = false;
	}

	if (!$logfile){
	    $error_occured = true;
	    $error_status = "LOG_FILE";
	    $message .= "Unable to open Aggregation_log.txt file!\n\r";
	    $file = false;
	}

	$date_time = date('Y-m-d H:i:s');
	if ($file) {
	    fwrite($logfile, "$date_time");
	    fwrite($logfile, " --> Aggregation started!! \r\n");
	}

	$message .= "$date_time --> Aggregation process started!! \r\n";

//get today's date id
	$today_date = date('Ymd');

//get date id from the last aggregation
	if (last_agg_day($logfile, $file, $dbconn, $last_agg_day, $message_last_agg_day, $error_occured)) {
	    $message .= $message_last_agg_day;

	    if (fill_aggDay($last_agg_day, $logfile, $file, $dbconn, $message_agg_day, $error_occured)) {
		$message .= $message_agg_day;
	    } else {
		$message .= $message_agg_day;
		if ($error_occured)
		    $error_status .= ", AGG_DAY";
		else
		    $error_status = "AGG_DAY";
		$error_occured = true;
	    }
	}
	else {
	    $message .= "Invalid last aggregation reading acquired.\r\n" . $message_last_agg_day;
	    if ($error_occured)
		$error_status .= ", LAST_AGG_DAY";
	    else
		$error_status = "LAST_AGG_DAY";
	    $error_occured = true;
	}


	if (last_agg_day_part($logfile, $file, $dbconn, $last_agg_day_part, $message_last_agg_day_part, $error_occured)) {
	    $message .= $message_last_agg_day_part;

	    if (fill_aggDayPart($last_agg_day_part, $logfile, $file, $dbconn, $message_agg_day_part, $error_occured)) {
		$message .= $message_agg_day_part;
	    } else {
		$message .= $message_agg_day_part;
		if ($error_occured)
		    $error_status .= ", AGG_DAY_PART";
		else
		    $error_status = "AGG_DAY_PART";
		$error_occured = true;
	    }
	}
	else {
	    $message .= "Invalid last_agg_day_part aggregation reading acquired.\r\n" . $message_last_agg_day_part;
	    if ($error_occured)
		$error_status .= ", LAST_AGG_DAY_PART";
	    else
		$error_status = "LAST_AGG_DAY_PART";
	    $error_occured = true;
	}

	//filling in hourly aggregation
	if (last_agg_day_hourly($logfile, $file, $dbconn, $last_agg_day_hourly, $message_last_agg_day_hourly, $error_occured)) {
	    $message .= $message_last_agg_day_hourly;

	    if (fill_aggDayHourly($last_agg_day_hourly, $logfile, $file, $dbconn, $message_agg_day_hourly, $error_occured)) {
		$message .= $message_agg_day_hourly;
	    } else {
		$message .= $message_agg_day_hourly;
		if ($error_occured)
		    $error_status .= ", AGG_DAY_HOURLY";
		else
		    $error_status = "AGG_DAY_HOURLY";
		$error_occured = true;
	    }
	}
	else {
	    $message .= "Invalid last_agg_day_hourly aggregation reading acquired.\r\n" . $message_last_agg_day_hourly;
	    if ($error_occured)
		$error_status .= ", LAST_AGG_DAY_HOURLY";
	    else
		$error_status = "LAST_AGG_DAY_HOURLY";
	    $error_occured = true;
	}

	//agg_month_day_part
	if (last_agg_month_day_part($logfile, $file, $dbconn, $last_agg_month_day_part, $message_last_agg_month_day_part, $error_occured)) {
	    $message .= $message_last_agg_month_day_part;
	    if (fill_agg_month_day_part($last_agg_month_day_part, $logfile, $file, $dbconn, $message_agg_month_day_part, $error_occured)) {
		$message .= $message_agg_month_day_part;
	    } else {
		$message .= $message_agg_month_day_part;
		if ($error_occured)
		    $error_status .= ", AGG_DAY_PART";
		else
		    $error_status = "AGG_DAY_PART";
		$error_occured = true;
	    }
	}
	else {
	    $message .= "Invalid last_agg_day_part aggregation reading acquired.\r\n" . $message_last_agg_day_part;
	    if ($error_occured)
		$error_status .= ", LAST_AGG_DAY_PART";
	    else
		$error_status = "LAST_AGG_DAY_PART";
	    $error_occured = true;
	}

	$date_time = date('Y-m-d H:i:s');

	if ($file) {
	    fwrite($logfile, "$date_time");
	    fwrite($logfile, " --> Aggregation finished! \r\n\r\n\r\n");
	    fclose($logfile);
	}

	$message .= "$date_time --> Aggregation finished!";

	pg_close($dbconn);
    } else {
	$message .= "ERROR: Unable to connect to the local database!";
	$error_occured = true;
	$error_status = "CONNECTION";
    }

    if ($error_occured) {
	$subject = "ERROR: " . $error_status . ", " . $subject;
    }

    mail($to, $subject, $message, $headers);
} catch (Exception $ae) {
    $subject = "ERROR " . $subject;
    $message .= "Unexpected error occured during this process! ERROR: " . $ae->getMessage();
    mail($to, $subject, $message, $headers);
}

/**
 * Function for getting date id of last successful aggregation
 * @param $log log file 
 * @param $dbconn database connection
 * @return date id or ends program in case of an error
 */
function last_agg_day($log, $file, $dbconn, &$last_agg_day, &$message, &$error_occured) {

    //statement for selecting date_id when the last aggregation happened
    $select = "Select MAX(date_id) as max FROM agg_day";
    //$select = "SELECT date_id FROM sys_date_of_aggregation WHERE type = 'day' ORDER BY date_id DESC LIMIT 1";

    $date_id = pg_query($dbconn, $select);
    if (!$date_id) {
	$message = "ERROR when selecting dateID of last successful aggregration on daily basis .." . pg_last_error() . ".. \r\n";
	$error_occured = true;

	if ($file)
	    fwrite($log, "ERROR when selecting dateID of last successful aggregration on daily basis .." . pg_last_error() . ".. \r\n");
	return false;
    }

    if (pg_field_is_null($date_id, "max") == 1) {
	$message = "No prior aggregations detected!\n\r";
	if ($file)
	    fwrite($log, "No prior aggregations detected!\n\r");
	$last_agg_day = '19000101';
	return true;
    }
    else {
	$date_id = pg_fetch_row($date_id);
	$last_agg_day = $date_id[0];

	$message = "Last aggregation occured " . $last_agg_day . "\r\n";
	if ($file)
	    fwrite($log, "Last aggregation occured " . $last_agg_day . "\r\n");
	return true;
    }
}

/**
 * Function for getting date id of last successful aggregation
 * @param $log log file 
 * @param $dbconn database connection
 * @return date id or ends program in case of an error
 */
function last_agg_day_part($log, $file, $dbconn, &$last_agg_day_part, &$message, &$error_occured) {

    //statement for selecting date_id when the last aggregation happened
    $select = "Select MAX(date_id) as max FROM agg_day_part";
    //$select = "SELECT date_id FROM sys_date_of_aggregation WHERE type = 'day part' ORDER BY date_id DESC LIMIT 1";

    $date_id = pg_query($dbconn, $select);
    if (!$date_id) {
	$message = "ERROR when selecting dateID of last successful aggregration on day_part .." . pg_last_error() . ".. \r\n";
	$error_occured = true;

	if ($file)
	    fwrite($log, "ERROR when selecting dateID of last successful aggregration on day_part .." . pg_last_error() . ".. \r\n");
	return false;
    }

    if (pg_field_is_null($date_id, "max") == 1) {
	$message = "No prior aggregations detected!\r\n";
	$last_agg_day_part = '19000101';
	if ($file)
	    fwrite($log, "No prior aggregations detected!\r\n");
	return true;
    }
    else {
	$date_id = pg_fetch_row($date_id);
	$last_agg_day_part = $date_id[0];

	$message = "Last aggregation occured " . $last_agg_day_part . "\r\n";
	if ($file)
	    fwrite($log, "Last aggregation occured " . $last_agg_day_part . "\r\n");
	return true;
    }
}

/**
 * Function for getting date id of last successful aggregation on agg_day_hourly
 * @param $log log file 
 * @param $dbconn database connection
 * @return date id or ends program in case of an error
 */
function last_agg_day_hourly($log, $file, $dbconn, &$last_agg_day_hourly, &$message, &$error_occured) {

    //statement for selecting date_id when the last aggregation happened
    $select = "Select MAX(date_id) as max FROM agg_day_hourly";
    //$select = "SELECT date_id FROM sys_date_of_aggregation WHERE type = 'day part' ORDER BY date_id DESC LIMIT 1";

    $date_id = pg_query($dbconn, $select);
    if (!$date_id) {
	$message = "ERROR when selecting dateID of last successful aggregration on hourly basis.." . pg_last_error() . ".. \r\n";
	$error_occured = true;

	if ($file)
	    fwrite($log, "ERROR when selecting dateID of last successful aggregration on hourly basis.." . pg_last_error() . ".. \r\n");
	return false;
    }

    if (pg_field_is_null($date_id, "max") == 1) {
	$message = "No prior aggregations detected!\r\n";
	$last_agg_day_hourly = '19000101';
	if ($file)
	    fwrite($log, "No prior aggregations detected!\r\n");
	return true;
    }
    else {
	$date_id = pg_fetch_row($date_id);
	$last_agg_day_hourly = $date_id[0];

	$message = "Last aggregation occured " . $last_agg_day_hourly . "\r\n";
	if ($file)
	    fwrite($log, "Last aggregation occured " . $last_agg_day_hourly . "\r\n");
	return true;
    }
}

/*
 *
 */
function last_agg_month_day_part($log, $file, $dbconn, &$last_agg_month_day_part, &$message, &$error_occured) {

    //statement for selecting date_id when the last aggregation happened
    $select = "Select MAX(month_id) as max FROM agg_month_day_part";
    //$select = "SELECT date_id FROM sys_date_of_aggregation WHERE type = 'day part' ORDER BY date_id DESC LIMIT 1";

    $date_id = pg_query($dbconn, $select);
    if (!$date_id) {
	$message = "ERROR when selecting month of last successful aggregration on month_day_part .." . pg_last_error() . ".. \r\n";
	$error_occured = true;

	if ($file)
	    fwrite($log, "ERROR when selecting month of last successful aggregration on month_day_part .." . pg_last_error() . ".. \r\n");
	return false;
    }

    if (pg_field_is_null($date_id, "max") == 1) {
	$message = "No prior aggregations detected!\n\r";
	$last_agg_month_day_part = '190001';
	if ($file)
	    fwrite($log, "No prior aggregations detected!\n\r");
	return true;
    }
    else {
	$date_id = pg_fetch_row($date_id);
	$last_agg_month_day_part = $date_id[0];

	$message = "Last aggregation occured " . $last_agg_month_day_part . "\r\n";
	if ($file)
	    fwrite($log, "Last aggregation occured " . $last_agg_month_day_part . "\r\n");
	return true;
    }
}

/**
 * Function for aggregating values on part of the day
 * @param $today today's date_id 
 * @param $last_agg date_id of last aggregation
 * @param $log log file 
 * @return date_id of the day of aggregation or false if error happens
 */
function fill_aggDayPart($last_agg, $log, $file, $dbconn,&$message, &$error_occured) {
    $delete_stmt = "DELETE FROM agg_day_part WHERE date_id >= " . $last_agg;
    $del = pg_query($dbconn, $delete_stmt);
    try {
	if (!$del) {
	    $error_occured = true;
	    $message = "ERROR when deleting last_agg date from agg_day_part!\n\r";
	    return false;
	}

	$insert_Agg_stmt = pg_prepare($dbconn, "insert_agg", "INSERT INTO agg_day_part (gsn_id, sensor_id, unit_id, date_id,
													day_part_id, avg_value, max_value, min_value, amplitude) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)");

	$query_agg = "SELECT gsn_id, sensor_id,  unit_id, date_id, day_part_id, AVG(value) as average,
					MAX(value) as maximum, MIN(value) as minimum, (MAX(value) - MIN(value)) as amplitude
				FROM f_readings fr join di_time t on t.time_id = fr.time_id WHERE date_id >= $last_agg
				GROUP BY gsn_id, sensor_id, unit_id, date_id, day_part_id
				ORDER BY date_id DESC,  day_part_id";

	$result = pg_query($dbconn, $query_agg);
	if (!$result) {
	    $message = "ERROR when aggregating values on day part...." . pg_last_error() . " \r\n";
	    $error_occured = true;

	    if ($file)
		fwrite($log, "ERROR when aggregating values on day part... " . pg_last_error() . " \r\n");
	    return false;
	}

	while ($reading_row = pg_fetch_row($result)) {

	    $res = pg_execute($dbconn, "insert_agg", array($reading_row[0], $reading_row[1], $reading_row[2], $reading_row[3],
			$reading_row[4], $reading_row[5], $reading_row[6], $reading_row[7], $reading_row[8]));

	    if (!$res) {
		$message = "Error in SQL query when INSERTING  into \"agg_day_part\" ......" . pg_last_error() . "\r\n";
		if ($file)
		    fwrite($log, "Error in SQL query when INSERTING  into \"agg_day_part\..." . pg_last_error() . "\r\n");
		$error_occured = true;
		return false;
	    }
	}

	$message = "Table agg_day_part inserting process went well!\r\n";
	if ($file)
	    fwrite($log, "Table agg_day_part inserting process went well!\r\n");
	return true;
    } catch (Exception $ae) {
	$message .= "\n\rUNEXPECTED ERROR OCCURED IN AGG_DAY_PART INSERT PROCESS! ERROR: " . $ae->getMessage();
	$error_occured = true;
	if ($file)
	    fwrite($log, "\n\rUNEXPECTED ERROR OCCURED IN AGG_DAY_PART INSERT PROCESS! ERROR: " . $ae->getMessage());
	return false;
    }
}


/**
 * Function for aggregating values on hourly basis
 * @param $today today's date_id 
 * @param $last_agg date_id of last aggregation
 * @param $log log file 
 * @return date_id of the day of aggregation or false if error happens
 */
function fill_aggDayHourly($last_agg, $log, $file, $dbconn,&$message, &$error_occured) {
    $delete_stmt = "DELETE FROM agg_day_hourly WHERE date_id >= " . $last_agg;
    $del = pg_query($dbconn, $delete_stmt);
    try {
	if (!$del) {
	    $error_occured = true;
	    $message = "ERROR when deleting last_agg date from agg_day_hourly !\n\r";
	    return false;
	}

	$insert_Agg_stmt = pg_prepare($dbconn, "insert_agg_hourly", "INSERT INTO agg_day_hourly (gsn_id, sensor_id, unit_id, date_id, hour, avg_value, max_value, min_value, amplitude) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)");

	$query_agg = "SELECT gsn_id, sensor_id,  unit_id, date_id, t.hour, AVG(value) as average,
					MAX(value) as maximum, MIN(value) as minimum, (MAX(value) - MIN(value)) as amplitude
				FROM f_readings fr join di_time t on t.time_id = fr.time_id WHERE date_id >= $last_agg
				GROUP BY gsn_id, sensor_id, unit_id, date_id, t.hour
				ORDER BY date_id DESC,  t.hour asc";

	$result = pg_query($dbconn, $query_agg);
	if (!$result) {
	    $message = "ERROR when aggregating values on day part...." . pg_last_error() . " \r\n";
	    $error_occured = true;

	    if ($file)
		fwrite($log, "ERROR when aggregating values on day part... " . pg_last_error() . " \r\n");
	    return false;
	}

	while ($reading_row = pg_fetch_row($result)) {

	    $res = pg_execute($dbconn, "insert_agg_hourly", array($reading_row[0], $reading_row[1], $reading_row[2], $reading_row[3],
			$reading_row[4], $reading_row[5], $reading_row[6], $reading_row[7], $reading_row[8]));

	    if (!$res) {
		$message = "Error in SQL query when INSERTING  into \"agg_day_hourly \" ......" . pg_last_error() . "\r\n";
		if ($file)
		    fwrite($log, "Error in SQL query when INSERTING  into \"agg_day_hourly \..." . pg_last_error() . "\r\n");
		$error_occured = true;
		return false;
	    }
	}

	$message = "Table agg_day_hourly inserting process went well!\r\n";
	if ($file)
	    fwrite($log, "Table agg_day_hourly inserting process went well!\r\n");
	return true;
    } catch (Exception $ae) {
	$message .= "\n\rUNEXPECTED ERROR OCCURED IN agg_day_hourly INSERT PROCESS! ERROR: " . $ae->getMessage();
	$error_occured = true;
	if ($file)
	    fwrite($log, "\n\rUNEXPECTED ERROR OCCURED IN agg_day_hourly INSERT PROCESS! ERROR: " . $ae->getMessage());
	return false;
    }
}

/**
 * Function for aggregating values on day
 * @param $today today's date_id 
 * @param $last_agg date_id of last aggregation
 * @param $log log file 
 * @return date_id of the day of aggregation or false if error happens
 */
function fill_aggDay($last_agg, $log, $file, $dbconn, &$message, &$error_occured) {
    $delete_stmt = "DELETE FROM agg_day WHERE date_id >= " . $last_agg;
    $del = pg_query($dbconn, $delete_stmt);

    try {
	if (!$del) {
	    $error_occured = true;
	    $message = "ERROR when deleting last_agg date from agg_day!\n\r";
	    return false;
	}

	$insert_AggDay_stmt = pg_prepare($dbconn, "insert_agg_day", "INSERT INTO agg_day (gsn_id, sensor_id, unit_id, date_id,
													avg_value, max_value, min_value, amplitude) VALUES ($1, $2, $3, $4, $5, $6, $7, $8)");

	$query_agg = "SELECT gsn_id, sensor_id,  unit_id, date_id, AVG(value) as average,
					MAX(value) as maximum, MIN(value) as minimum, (MAX(value) - MIN(value)) as amplitude
				FROM f_readings WHERE date_id >= $last_agg
				GROUP BY gsn_id, sensor_id, unit_id, date_id
				ORDER BY date_id DESC";



	$result = pg_query($dbconn, $query_agg);
	if (!$result) {
	    $message = "ERROR when aggregating values on daily basis...." . pg_last_error() . " \r\n";
	    $error_occured = true;

	    if ($file)
		fwrite($log, "ERROR when aggregating values on daily basis... " . pg_last_error() . " \r\n");
	    return false;
	}

	while ($reading_row = pg_fetch_row($result)) {

	    $res = pg_execute($dbconn, "insert_agg_day", array($reading_row[0], $reading_row[1], $reading_row[2], $reading_row[3],
			$reading_row[4], $reading_row[5], $reading_row[6], $reading_row[7]));

	    if (!$res) {
		$message = "Error in SQL query when INSERTING  into \"agg_day\" ......" . pg_last_error() . "\r\n";
		if ($file)
		    fwrite($log, "Error in SQL query when INSERTING  into \"agg_day\..." . pg_last_error() . "\r\n");
		$error_occured = true;
		return false;
	    }
	}

	$message = "Table agg_day inserting process went well!\r\n";
	if ($file)
	    fwrite($log, "Table agg_day inserting process went well!\r\n");
	return true;
    } catch (Exception $ae) {
	$message .= "\n\rUNEXPECTED ERROR OCCURED IN AGG_DAY INSERT PROCESS! ERROR: " . $ae->getMessage();
	$error_occured = true;
	if ($file)
	    fwrite($log, "\n\rUNEXPECTED ERROR OCCURED IN AGG_DAY INSERT PROCESS! ERROR: " . $ae->getMessage());
	return false;
    }
}

function fill_agg_month_day_part($last_agg, $log, $file, $dbconn, &$message, &$error_occured) {
    $delete_stmt = "DELETE FROM agg_month_day_part WHERE month_id >= " . $last_agg;
    $del = pg_query($dbconn, $delete_stmt);

    try {
	if (!$del) {
	    $error_occured = true;
	    $message = "ERROR when deleting last_agg date from agg_month_day_part!\n\r";
	    return false;
	}

	$insert_AggDay_stmt = pg_prepare($dbconn, "insert_agg_month_day_part", "INSERT INTO agg_month_day_part (gsn_id, sensor_id, unit_id, day_part_id, year, month, month_id,
													avg_value, max_value, min_value, amplitude) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11)");

	$query_agg = "SELECT a.gsn_id, a.sensor_id, a.unit_id, a.day_part_id, d.year, d.month, d.year_month, AVG(avg_value) as avg_value,
					MAX(max_value) as max_value, MIN(min_value) as min_value, (MAX(max_value) - MIN(min_value)) as amplitude
				FROM agg_day_part a
				JOIN di_days d ON a.date_id = d.date_id 
				WHERE
				    1 = 1
				AND a.date_id >= ".$last_agg."01
				GROUP BY a.gsn_id, a.sensor_id, a.unit_id, a.day_part_id, d.year, d.month, d.year_month
				ORDER BY a.gsn_id, a.sensor_id, a.unit_id, d.year asc, d.month asc, a.day_part_id asc";



	$result = pg_query($dbconn, $query_agg);

	if (!$result) {
	    $message = "ERROR when aggregating values on daily basis...." . pg_last_error() . " \r\n";
	    $error_occured = true;

	    if ($file)
		fwrite($log, "ERROR when aggregating values on daily basis... " . pg_last_error() . " \r\n");
	    return false;
	}

	while ($reading_row = pg_fetch_row($result)) {

	    $res = pg_execute($dbconn, "insert_agg_month_day_part", array($reading_row[0], $reading_row[1], $reading_row[2], $reading_row[3],
			$reading_row[4], $reading_row[5], $reading_row[6], $reading_row[7], $reading_row[8], $reading_row[9], $reading_row[10]));

	    if (!$res) {
		$message = "Error in SQL query when INSERTING  into \"agg_month_day_part\" ......" . pg_last_error() . "\r\n";
		if ($file)
		    fwrite($log, "Error in SQL query when INSERTING  into \"agg_month_day_part\"..." . pg_last_error() . "\r\n");
		$error_occured = true;
		return false;
	    }
	}

	$message = "Table agg_month_day_part inserting process went well!\r\n";
	if ($file)
	    fwrite($log, "Table agg_month_day_part inserting process went well!\r\n");
	return true;
    } catch (Exception $ae) {
	$message .= "\n\rUNEXPECTED ERROR OCCURED IN AGG_MONTH_DAY_PART INSERT PROCESS! ERROR: " . $ae->getMessage();
	$error_occured = true;
	if ($file)
	    fwrite($log, "\n\rUNEXPECTED ERROR OCCURED IN AGG_MONTH_DAY_PART INSERT PROCESS! ERROR: " . $ae->getMessage());
	return false;
    }
}

?>