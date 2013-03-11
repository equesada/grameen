<?php
	require_once "conf.php";
	require_once "func.php";	
	$edu_title = isset($_GET['edu_title']) ? str_clean($_GET['edu_title']) : "";
	$status = isset($_GET['status']) ? str_clean($_GET['status'], 1) : 1;
	$country_id = isset($_GET['country_id']) ? str_clean($_GET['country_id']) : "ID";
	$sql = 'INSERT INTO education(edu_title, status, date_add, date_update, country_id) VALUES ("'.$edu_title.'",'.$status.', now(), now(), "'.$country_id.'")';
	$r = mysql_query($sql) OR die(output(mysql_error()));
	echo output(1);
?>