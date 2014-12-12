<?php

/*********************************************************
* Time measurement
**********************************************************/
$parse_time_start = microtime(true);

/*********************************************************
* Directory separator
**********************************************************/
define('DIR_SEP', DIRECTORY_SEPARATOR);

/*********************************************************
* Save a few paths to constants
**********************************************************/
// Find out path to the basic directory
// condition: this file is in the first layer of the project
$current_path = realpath( dirname(__FILE__) );
$absolute_path = '';
$pieces = explode(DIR_SEP, $current_path);
$number_of_dirs = count($pieces);
for ($i = 0; $i < $number_of_dirs - 1; $i++) {
	$absolute_path .= $pieces[$i] . DIR_SEP;
}
define('ROOT_DIR'       , $absolute_path);
define('ROOT_URL'       , 'http://' . $_SERVER['HTTP_HOST'] . '/');
define('HTDOCS_DIR'     , ROOT_DIR . 'htdocs' . DIR_SEP);
define('CONFIG_DIR'     , ROOT_DIR . '_config' . DIR_SEP);
define('SRC_DIR'        , ROOT_DIR . '_src' . DIR_SEP);
define('LIB_DIR'        , ROOT_DIR . '_lib' . DIR_SEP);
define('CACHE_DIR'      , ROOT_DIR . '_cache' . DIR_SEP);

/*********************************************************
* Include config
**********************************************************/
require_once(CONFIG_DIR . 'config.php');

/*********************************************************
* Class includes
**********************************************************/
require_once(LIB_DIR . 'smartAutoload.class.php');
function __autoload($class_name) {
	static $smartAutoload;
	if (!$smartAutoload) {
		$smartAutoload = new smartAutoload();
		$smartAutoload->setCacheFilename(CACHE_DIR . 'smartAutoloadCache.php');
		$smartAutoload->addDirectory(array(LIB_DIR));
		$smartAutoload->addFileEnding('.class.php');
	}
	$smartAutoload->loadClass($class_name);
}

/*********************************************************
* Start the process
**********************************************************/

// Include main util functions
require_once(SRC_DIR . 'util.php');

// Start buffering output
ob_start();

// Time measurement
$parse_time_end = microtime(true);
$parse_time = round($parse_time_end - $parse_time_start, 6);

?>