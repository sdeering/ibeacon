<?php

// Set timezone
date_default_timezone_set('Australia/Queensland');

// Set DEV mode
include_once('ENV.php'); //DEV, PREVIEW, PROD

// Init MySQL connection
// Use debug statements below to test DB connection.
try{

	$db = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASSWORD, array(
	    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	    PDO::ATTR_PERSISTENT => true
	));
    //die(json_encode(array('outcome' => true))); //connected test
}
catch(PDOException $ex){
    //die(json_encode(array('outcome' => false, 'message' => 'Unable to connect to database.')));
    echo "Unable to connect to database.";
}

?>