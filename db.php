<?php

	define('DEV', true);

	if (DEV) {

		// php.ini Settings
		//ini_set('display_errors', 'On');

		// Error Level
		//error_reporting(E_ALL);

		define('DB_SERVER'  , 'localhost');
		define('DB_USER'    , 'root');
		define('DB_PASSWORD', 'root');
		define('DB_NAME'    , 'ibeacon');

	} else {

		define('DB_SERVER','localhost');
		define('DB_USER','root');
		define('DB_PASSWORD','bkti3O0RMqmz');
		define('DB_NAME','ibeacon');

	} 

	// Init MySQL connection
	$db = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASSWORD);

?>