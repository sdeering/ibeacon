<?php

// Stop buffering
$output = ob_get_contents();
ob_end_clean();

// Optimize output (remove HTML comments)
/*
if (!DEV) {
	$output = preg_replace('#<!--(?!<!)[^\[>].*?-->#Uis', '', $output);
}
*/

// Flush!
echo $output;

?>