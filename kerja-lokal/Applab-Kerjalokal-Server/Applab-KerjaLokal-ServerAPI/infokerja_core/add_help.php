<?php
	require_once "conf.php";
	require_once "func.php";	
	$help_title = isset($_GET['help_title']) ? str_clean($_GET['help_title']) : "";
	$description = isset($_GET['description']) ? str_clean($_GET['description']) : "";
	$status = isset($_GET['status']) ? str_clean($_GET['status'], 1) : 1;
	$country_id = isset($_GET['country_id']) ? str_clean($_GET['country_id']) : "ID";
	$sql = 'INSERT INTO help(help_title, description, status, date_add, date_update, country_id) VALUES ("'.$help_title.'","'.$description.'",'.$status.', now(), now(), "'.$country_id.'")';
	
	$r = mysql_query($sql) OR die(output(mysql_error()));
	echo output(1);
?>