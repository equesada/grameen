<?php

	require "conf.php";
	require "func.php";

	$mdn = isset($_GET["mdn"]) ? str_clean($_GET["mdn"]) : "";
	$pin = isset($_GET["pin"]) ? str_clean($_GET["pin"]) : "";

	$arr = array();
	
	$sql = "SELECT * FROM $t_mentors WHERE mdn='$mdn' AND pin='$pin'";
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	
	if (mysql_num_rows($sql) == 1)
		echo output(1);
	else
		echo output("Invalid MDN or PIN");
	

?>