<?php

include "database_connection.php";

$message = "";
$subject = "Cron_job_ETL_sensors_readings";
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

$error_occured = false;
$temp_error_occured = false;
$error_status = "";

include "manageSensorsFromGsn.php";
include "acquireDataFromGsn.php";

$query = "SELECT gsn_id from di_gsn WHERE is_active = '1' and is_dummy != '1'";

try {
    if ($database_connection_status) {
	$result = pg_query($dbconn, $query);
	if (!$result) {
	    $error_occured = true;
	    $error_status = "GSN_SERVERS";
	    $message .= "ERROR: While fetching active GSN servers...." . pg_last_error();
	} else {
	    while ($active_GSN = pg_fetch_row($result)) {
		try {
		    $message_adding = "";
		    $error_message ="";
			$temp_error_occured = false;
		    if (add_sensor($active_GSN[0], $message_adding, $temp_error_occured, $error_message)) {
			$message .= $message_adding."Adding sensor process succesfully finished!\n";
		    } else {
			$error_occured = true;
			$error_status = "ADDING_SENSORS";
			$message .= $message_adding. "ERROR: Unable to load XML or update sensors in database! Please try the connection manually for gsn_id = " . $active_GSN[0] . "!\r\nDetails about error: ".$error_message."\r\n";
		    }
		    $message .= "\r\n";

		    $message_extract = "";
			$temp_error_occured = false;
		    if (extract_readings($active_GSN[0], $message_extract, $temp_error_occured)) {
			$message .= $message_extract."Extracting readings successfuly finished!\n\r";
		    } else {
			if ($error_occured)
			    $error_status .= ", EXTRACTING_DATA";
			else {
			    $error_occured = true;
			    $error_status = "EXTRACTING DATA";
			}
			$message .=  $message_extract."\nERROR: PROBLEM OCCURED WHILE EXTRACTING DATA!\n\r";
		    }
		} catch (Exception $ae) {
		    if ($error_occured) {
			$error_status .= ", UNEXPECTED";
		    } else {
			$error_occured = true;
			$error_status = "UNEXPECTED";
		    }
		    $message .= "ERROR: Unable to load XML or add data from database! Please try the connection manually for gsn_id = " . $active_GSN[0] . "!\n";
		}
	    }
	}
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
    $message .= "Unexpected error occured during this proces. ERROR: " . $ae->getMessage;
    mail($to, $subject, $message, $headers);
}
?>