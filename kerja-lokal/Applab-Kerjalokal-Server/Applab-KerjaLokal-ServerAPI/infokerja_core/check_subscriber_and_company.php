<?php

	require "conf.php";
	require "func.php";

	$subscriber_id = isset($_GET["subscriber_id"]) ? str_clean($_GET["subscriber_id"]) : "";
	$comp_id = isset($_GET["comp_id"]) ? str_clean($_GET["comp_id"]) : "";

	$arr = array();
	
	$sql = "SELECT * FROM $t_rel_subscriber_company WHERE subscriber_id='$subscriber_id' AND comp_id='$comp_id'";
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	
	if (mysql_num_rows($sql) == 1)
		echo output(1);
	else
		echo output("Record has already exists.");
	

?>