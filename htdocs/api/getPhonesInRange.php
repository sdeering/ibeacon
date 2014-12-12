<?php 
	
	include_once('../../_src/setup.php');

	//list phones active on ibeacon
 	$statement = $db->prepare("select distinct phone_id from phones_in_range where active = :active");
	$statement->execute(array(':active' => 1));
	$result = $statement->fetchAll();
	//var_dump($result);

	$html = '';
	if ($result) {
		foreach ($result as $key => $value) {
			$html .= '<img class="phonepic" src="img/phone'.$value["phone_id"].'.png" />';
		}	
	} else {
		$html = "<p>No phones detected.</p>";
	}

    echo json_encode(array('data' => $result, 'html' => $html));

    include_once('../../_src/flush.php');

?>