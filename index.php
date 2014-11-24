<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>IBEACON</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/main.css">
		<script>
		     function refresh() {
		         window.location.reload(true);
		     }
		     setTimeout(refresh, 5000);
		</script>
    </head>
    <body>
		<img src="img/header.png" />
		<div class="phones">
		<?php

			require_once('util.php');
			require_once('db.php');

			//list phones active on ibeacon
		 	$statement = $db->prepare("select distinct phone_id from phones_in_range where active = :active");
			$statement->execute(array(':active' => 1));
			$result = $statement->fetchAll();
			//var_dump($result);

			if ($result) {
				foreach ($result as $key => $value) {
					echo '<img class="phonepic" src="img/phone'.$value["phone_id"].'.png" />';
				}	
			} else {
				echo "No phones detected.";
			}

		?>
		</div>
    </body>
</html>