<?php
	require_once "conf.php";
	require_once "func.php";	
	$tips_title = isset($_GET['tips_title']) ? str_clean($_GET['tips_title']) : "";
	$description = isset($_GET['description']) ? str_clean($_GET['description']) : "";
	$status = isset($_GET['status']) ? str_clean($_GET['status'], 1) : 1;
	$country_id = isset($_GET['country_id']) ? str_clean($_GET['country_id']) : "ID";
	$sql = 'INSERT INTO tips(tips_title, description, status, date_add, date_update, country_id) VALUES ("'.$tips_title.'","'.$description.'",'.$status.', now(), now(), "'.$country_id.'")';
	//die($sql);
	$r = mysql_query($sql) OR die(output(mysql_error()));
	echo output(1);
?>