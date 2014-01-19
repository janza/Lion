<?php

include "database_connection.php";

$error_occured = false;
$error_status = "";
$message = "";
$subject = "Daily reports";
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

if ($database_connection_status) {
//select all sensors we need to make reports for
    $query = "select distinct gsn_id, sensor_id from daily_reports WHERE is_active = '1' and is_sending = '1'";
    $readings = pg_query($dbconn, $query);

    $num = pg_num_rows($readings);

    if ($num > 0) {
	$message .= "<---------Daily report process started on " . date("d.m.Y h:i:s") . "----------->";
//for every sensor_id we need to make our report first
	while ($reading_row = pg_fetch_row($readings)) {

	    //this report is created and saved in the appropriate folder
	    exec('xvfb-run -a -s "-screen 0 640x480x16" wkhtmltopdf --dpi 200 --redirect-delay 2000 --page-size A4 "http://161.53.67.224/lion/index.php/webService/dailyReportGenerating?gsn_list=' . $reading_row[0] . '&sensor_list=' . $reading_row[1] . '&date_list=' . date('Ymd',strtotime ( '-1 day' , strtotime (date('Y-m-d')))) . '" "/home/lpostruzin/cron_new/reports/' . "Daily_report_" . $reading_row[0] . "_GSN_" . $reading_row[1] .'_'. date('Ymd',strtotime ( '-1 day' , strtotime (date('Y-m-d')))) . '.pdf"');
	}

	$users = "select distinct m.email, u.first_name||' '||u.last_name as full_name from daily_reports m JOIN prod_users u ON m.user_id = u.user_id WHERE m.is_active = '1' and is_sending = '1'";
	$emails = pg_query($dbconn, $users);

	while ($emails_row = pg_fetch_row($emails)) {

	    $query = "select gsn_id, sensor_id, m.email, u.username, u.first_name||' '||u.last_name as full_name from daily_reports m JOIN prod_users u ON m.user_id = u.user_id WHERE m.is_active = '1' and is_sending = '1' and m.email = '" . $emails_row[0] . "'";
	    $readings = pg_query($dbconn, $query);

	    $command = 'mutt -s "Daily report for ' . $emails_row[1] . ', ' . date('d.m.Y',strtotime ( '-1 day' , strtotime (date('Y-m-d')))) . '" ' . $emails_row[0];

	    $num = pg_num_rows($readings);

	    //go through every report for our user and save the file to the email as attachment
	    while ($reading_row = pg_fetch_row($readings)) {
		$message .= "\n\rEmail: ".$emails_row[0] . ", report for sensor ".$reading_row[1]. ", on GSN ".$reading_row[0];
		
		$command.=' -a ' . '"/home/lpostruzin/cron_new/reports/' . 'Daily_report_' . $reading_row[0] . '_GSN_' . $reading_row[1] .'_'. date("Ymd",strtotime ( '-1 day' , strtotime (date('Y-m-d')))) . '.pdf"';
	    }

	    //echo $command . "\n";

	    try {
		exec($command);
	    } catch (Exception $ae) {
		$message .= "Problem occured while executing command: \r\n" . $command . "\r\nERROR: " . $ae->getMessage() . "\r\n";
		$error_occured = true;
		$error_status = "SENDING";
	    }
	}

	/*FOLLOWING CODE IS USED TO DELETE REPORTS WE HAVE GENERATED. FOR NOW, ALL REPORTS ARE SAVED SO WE CAN REVIEW ANY ERROR THAT OCCURED!*/
	
	  //select all readings from l_readings apart from today's readings
	  $query = "select distinct gsn_id, sensor_id from daily_reports WHERE is_active = '1' and is_sending = '1'";
	  $readings = pg_query ($dbconn, $query);

	  $num = pg_num_rows($readings);

	  //go through every reading and store it in f_readings
	  while ($reading_row = pg_fetch_row($readings)) {

	  //exec('wget "http://161.53.67.224/lion/index.php?r=webService/monthlyReportGenerating&gsn_list='.$reading_row[0].'&sensor_list='.$reading_row[1].'&year_list=201204"');
	  exec('rm "/home/lpostruzin/cron_new/reports'."Daily_report_" . $reading_row[0] ."_GSN_".$reading_row[1].'_'.date("Ymd",strtotime ( '-1 day' , strtotime (date('Y-m-d')))).".pdf\"");

	  }
	 
//exec('mv "index.php?r=webService%2FmonthlyReportGenerating&gsn_list=2&sensor_list=10&year_list=201204" Monthly_report.pdf');

	$message .= "\r\n<---------Daily report process finished on " . date("d.m.Y h:i:s") . "----------->";
    } else {
	$message .= "There are no daily reports we need to send today!\r\n";
    }
} else {
    $error_occured = true;
    $error_status = "DATABASE CONNECTION";
    $messsage = "Unable to connect to the local database, please check the problem manually!";
}

if ($error_occured) {
    $subject = "ERROR: " . $error_status . ", " . $subject;
}

mail($to, $subject, $message, $headers);
?>