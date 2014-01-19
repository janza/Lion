<?php
	
	include "database_connection.php";
	
	//open log file
	$file_name = "Aggregation_month_day_part_log.txt";
	$logfile = fopen($file_name, 'a');
	
	$date_time = date('Y-m-d H:i:s');
	fwrite($logfile, "$date_time");
	fwrite($logfile, " --> Aggregation on monthly basis started!! \r\n");
	echo ("$date_time --> Aggregation on monthly basis started!! \r\n");
	
	//delete previous values in agg_month_day_part
	$query = "Truncate table agg_month_day_part";
	$res = pg_query ($dbconn, $query);
	if(!$res){
		echo("ERROR when truncating values in agg_month_day_part..." . pg_last_error() . "\r\n");
		fwrite($logfile, "ERROR when truncating values in agg_month_day_part..." . pg_last_error() . " \r\n\r\n\r\n");
		die;
	}
	
	//statements
	$insert_AggDay_stmt = pg_prepare($dbconn, "insert", "INSERT INTO agg_month_day_part (gsn_id, sensor_id, unit_id, year, month, day_part_id,
													avg_value, avg_max_value, avg_min_value, avg_amplitude) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)");

	$query_agg = "SELECT agg.gsn_id, agg.sensor_id,  agg.unit_id, d.year, d.month, d.day_part_id, AVG(avg_value) as prosjek,
							AVG(max_value) as maximum, AVG(min_value) as minimum, AVG(amplitude) as amplitude
					FROM agg_day_part agg JOIN di_days d ON d.date_id = agg.date_id
					GROUP BY agg.gsn_id, agg.sensor_id,  agg.unit_id, d.year, d.month, d.day_part_id
					ORDER BY d.year, d.month, d.day_part_id";
	
	
	//aggregate values
	$result = pg_query ($dbconn, $query_agg);
	if(!$result){
		echo ("ERROR when aggregating values for day part on monthly basis ... " . pg_last_error() . "\r\n");
		fwrite($logfile, "ERROR when aggregating values for day part on monthly basis ..." . pg_last_error() . "\r\n\r\n\r\n");
		
	}
	
	//go through every row and store it in agg_month_day_part
	while($reading_row = pg_fetch_row($result)){
		
		$res = pg_execute ($dbconn, "insert", array($reading_row[0], $reading_row[1], $reading_row[2], $reading_row[3], 
													$reading_row[4], $reading_row[5], $reading_row[6], $reading_row[7], $reading_row[8], $reading_row[9]));
													
		if(!$res) {
						echo ("Error in SQL query when INSERTING  into \"agg_month_day_part\" ......"  . pg_last_error() . "\r\n");
						fwrite($logfile, "Error in SQL query when INSERTING  into \"agg_month_day_part\" ..... " . pg_last_error() . "\r\n");
						echo "<br/>";
					}      
	}
	
	$date_time = date('Y-m-d H:i:s');
	fwrite($logfile, "$date_time");
	fwrite($logfile, " --> Aggregation finished! \r\n\r\n\r\n");
	fclose($logfile);	
	echo ("$date_time --> Aggregation finished! \r\n\r\n");
	
	pg_close($dbconn);
	
?>	
				