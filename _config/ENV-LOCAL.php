<?php
	/***** DEVELOPMENT SETTINGS *****/

	define('ENV', 'DEV');
	
	define('DB_SERVER'  , 'localhost');
	define('DB_USER'    , 'root');
	define('DB_PASSWORD', 'root');
	define('DB_NAME'    , 'ibeacon');

	// php.ini Settings
	ini_set('display_errors', 'On');

	// Error Level
	error_reporting(E_ALL);
?>