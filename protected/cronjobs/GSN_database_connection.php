<?php

function GSN_DB_connection($GSN_ID){
	include "database_connection.php";

	$response = array();
	
	$query = "SELECT gsn_ip, database_port, database_user, database_password, gsn_name from di_gsn WHERE gsn_id = $GSN_ID";
	$result = pg_query($dbconn, $query);
	if(!$result){
		echo ("ERROR when fetching data for connectng to GSN's database.... " . pg_last_error());
		exit();
	}
	$GSN_db_info = pg_fetch_row($result);
	
	try{
		$dbconnGSN = pg_connect("host=$GSN_db_info[0] port=$GSN_db_info[1] user=$GSN_db_info[2] password=$GSN_db_info[3]");

		if (!$dbconnGSN){
			return false;
			}
	}
	catch (Exception $ae){
		return false;
	}
	array_push($response, $dbconnGSN);
	array_push($response, $GSN_db_info[4]);
	
	return $response;
}	
?>