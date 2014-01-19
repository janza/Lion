<?php

include "database_connection.php";
$file = true;
$error_occured = false;
$message = "";
$subject = "Cron_fact_table_filler";
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

//statement for inserting values in f_readings
$insert_stmt = pg_prepare($dbconn, "is", "INSERT INTO f_readings (gsn_id, sensor_id, unit_id, date_id, time_id,
													time_of_the_reading, value ) VALUES ($1, $2, $3, $4, $5, $6, $7)");

try {
    if ($database_connection_status) {
	//open log file
	$file_name = "Fact_table_filler_log.txt";
	$logfile = fopen($file_name, 'a');

	if (!$logfile)
	    $file = false;

	$date_time = date('Y-m-d H:i:s');
	//get todays date
	$dateID = date('Ymd');

	if ($file) {
	    fwrite($logfile, "$date_time");
	    fwrite($logfile, " --> Extraction started for days before " . $dateID . "!! \r\n");
	}
	$message .= "$date_time Extraction started for days before " . $dateID . "!! \r\n";

	//select all readings from l_readings apart from today's readings
	$query = "Select gsn_id, sensor_id, unit_id, date_id, time_id, time_of_the_reading, value from l_readings WHERE date_id < $dateID ORDER BY reading_id";
	$readings = pg_query($dbconn, $query);

	$num = pg_num_rows($readings);
	if ($file) {
	    fwrite($logfile, "$num values extracted from l_readings to f_readings... \r\n");
	}

	$message .= "$num values extracting from l_readings to f_readings... \r\n";

	if ($num > 0) {
	    //go through every reading and store it in f_readings
	    while ($reading_row = pg_fetch_row($readings)) {
		$result = pg_execute($dbconn, "is", array($reading_row[0], $reading_row[1], $reading_row[2], $reading_row[3],
			    $reading_row[4], $reading_row[5], $reading_row[6]));
		if (!$result) {
		    $error_status = "INSERT";

		    $message .= "Error in SQL query when INSERTING  into f_readings ...." . pg_last_error() . "\r\n";
		    fwrite($logfile, "Error in SQL query when INSERTING  into f_readings ...." . pg_last_error() . "\r\n");
		    $error_occured = true;
		}
	    }

	    if (!$error_occured) {
		//delete extracted readings from l_readings
		$truncate_query = "Delete from l_readings where date_id < " . $dateID;
		$result = pg_query($dbconn, $truncate_query);
		if (!$result) {
		    $error_occured = true;
		    $error_status = "DELETE";
		    $message .= "Error when deleting readings from l_readings! " . pg_last_error() . "\r\n";
		    fwrite($logfile, "Error when deleting readings from l_readings! " . pg_last_error() . "\r\n");
		}
	    }
	} else {
	    $message .= "There were no readings to insert into f_readings table!\r\n";
	}

	$date_time = date('Y-m-d H:i:s');

	if ($file) {
	    fwrite($logfile, "$date_time");
	    fwrite($logfile, " --> Extraction finished! \r\n");
	}
	$message .= "$date_time --> Extraction finished! \r\n";

	pg_close($dbconn);
    } else {
	$message .= "Unable to connect to the local database, please review the problem manually!\r\n";
	if ($file) {
	    fwrite($logfile, "Unable to connect to the local database, please review the problem manually!\r\n");
	}

	$error_occured = true;
    }
} catch (Exception $ae) {
    if ($error_occured)
	$error_status .= "UNKNOWN";
    else
	$error_status = "UNKNOWN";
    $error_occured = true;
    $message .= "Unexpected error occured- ERROR: " . $ae->getMessage();
    if ($file) {
	fwrite($logfile, "Unexpected error occured- ERROR: " . $ae->getMessage());
    }
}

if ($error_occured) {
    $subject = "ERROR: " . $error_status . ", " . $subject;
}

mail($to, $subject, $message, $headers);
?>