<?php

	require "conf.php";
	require "func.php";

	$sql = "SELECT * FROM $t_broadcast_sms ORDER BY $t_broadcast_sms.sms_id DESC LIMIT 0,1";
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	
	
	$arr = array();
	
	$row = mysql_fetch_assoc($sql);
	
	$arr["sms_id"] = $row["sms_id"];

	echo output($arr);

?>