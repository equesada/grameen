<?php

	require "conf.php";
	require "func.php";

	$mdn = isset($_GET["mdn"]) ? str_clean($_GET["mdn"]) : "";
	$type = isset($_GET["type"]) ? str_clean($_GET["type"]) : "";

	$arr = array();
	
	//$sql = "SELECT * FROM $t_".$type."s WHERE mdn='$mdn'";
	$sql = "SELECT * FROM ".$type."s WHERE mdn='$mdn'";
	//die($sql);
	//$sql = "SELECT * FROM mentors WHERE mdn='$mdn'";
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	if (mysql_num_rows($sql) == 1)
		echo output(1);
	else
		echo output("Invalid MDN");

?>