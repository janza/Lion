<?php
//here you can change parameters for local database connection
$database_connection_status = true;
$dbconn = pg_connect("host=localhost port=5432 user=coldwatch password=coldwatch dbname=coldwatchDB");

if (!$dbconn) {
    //die("Could not open connection to database server");
    $database_connection_status = false;
}
?>