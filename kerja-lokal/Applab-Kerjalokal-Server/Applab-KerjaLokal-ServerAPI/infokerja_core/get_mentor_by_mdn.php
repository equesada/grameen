<?php

	require "conf.php";
	require "func.php";

	$mdn = isset($_GET["mdn"]) ? str_clean($_GET["mdn"]) : 0;

	$sql = "SELECT * FROM $t_mentors WHERE mdn='$mdn'";
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$row = mysql_fetch_assoc($sql);
	
	for($j=0; $j<mysql_num_fields($sql); $j++)
	{
		$arr[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
	}

	echo output($arr);

?>