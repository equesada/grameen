<?php

	require "conf.php";
	require "func.php";

	$subscriber_id = isset($_GET["subscriber_id"]) ? str_clean($_GET["subscriber_id"]) : 0;

	$sql = "SELECT * FROM $t_rel_subscriber_cat WHERE subscriber_id='$subscriber_id' ORDER BY $t_rel_subscriber_cat.date_expired DESC LIMIT 0,1";
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	
	
	$arr = array();
	
	$row = mysql_fetch_assoc($sql);
	
	$arr["date_expired"] = $row["date_expired"];

	echo output($arr);

?>