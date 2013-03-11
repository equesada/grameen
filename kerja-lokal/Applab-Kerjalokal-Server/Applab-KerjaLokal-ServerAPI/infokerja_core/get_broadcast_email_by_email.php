<?php

	require "conf.php";
	require "func.php";

	$subscriber_id = isset($_GET["subscriber_id"]) ? str_clean($_GET["subscriber_id"]) : 0;

	$sql = "SELECT * FROM $t_broadcast_email ORDER BY $t_broadcast_email.email_id DESC LIMIT 0,1";
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	
	
	$arr = array();
	
	$row = mysql_fetch_assoc($sql);
	
	$arr["email_id"] = $row["email_id"];

	echo output($arr);

?>