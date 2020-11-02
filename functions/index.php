<?php

	if (!empty($_SERVER['SCRIPT_FILENAME']) and basename($_SERVER['PHP_SELF']) == __FILE__) die(header("HTTP/1.0 404 Not Found"));

	require_once('date.php');
	require_once('domains.php');
	require_once('validate.php');
	require_once('numbers.php');
	require_once('objects.php');
	require_once('strings.php');
	require_once('miscellaneous.php');
	require_once('wordpress.php');

?>