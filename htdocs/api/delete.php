<?php

	require_once('util.php');
	require_once('db.php');

	//add a phone as active on i beacon

	$REQUEST_VARS = _getvars();
	//var_dump($REQUEST_VARS);

	$phoneId = $REQUEST_VARS['GET']['phoneId'];
	$query = "UPDATE `ibeacon`.`phones_in_range` SET `active` = '0' WHERE `phones_in_range`.`phone_id` = ".$phoneId.";";
	//echo $query;
	$result = $db->exec($query);
	if ($result) {
		echo "Phone ".$phoneId." removed.";
	} else {
		echo "error removing phone.";
	}

?>