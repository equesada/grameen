<?php

	require "conf.php";
	require "func.php";

	$pin = isset($_GET["pin"]) ? str_clean($_GET["pin"],1) : "1";
	
	
	//$id = isset($_GET["id"]) ? str_clean($_GET["id"],1) : 0;

	$sql = "SELECT * FROM $t_mentors WHERE pin='$pin'";
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	
	$arr = array();
	
	$row = mysql_fetch_assoc($sql);
	
	$arr["pin"] = $row["pin"];

	echo output($arr);
	
?>