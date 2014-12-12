<?php

	include_once('../../_src/setup.php');

	//add a phone as active on i beacon

	$REQUEST_VARS = _getvars();
	//var_dump($REQUEST_VARS);

	$phoneId = $REQUEST_VARS['GET']['phoneId'];
	$query = "INSERT INTO `ibeacon`.`phones_in_range` (`phone_id`, `active`, `timestamp`) VALUES ('".$phoneId."', '1', CURRENT_TIMESTAMP)";
	//echo $query;
	$result = $db->exec($query);
	if ($result) {
		echo "Phone ".$phoneId." added.";
	} else {
		echo "error adding phone.";
	}

	include_once('../../_src/flush.php');

?>