<?php
	require_once "conf.php";
	require_once "func.php";
	
	$jobcat_title = isset($_GET['jobcat_title']) ? str_clean($_GET['jobcat_title']) : "";
	$description = isset($_GET['description']) ? str_clean($_GET['description']) : "";
	$sql = 'INSERT INTO jobs_category(jobcat_title, description, date_add, date_update) VALUES ("'.$jobcat_title.'", "'.$description.'", now(), now())';
	//die($sql);
	$r = mysql_query($sql) OR die(output(mysql_error()));
	echo output(1);
?>