<?php

	require "conf.php";
	require "func.php";

	//echo "<pre>";print_r($_GET);
	//$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$webdms = isset($_GET["webdms"]) ? str_clean($_GET["webdms"]) : "web";
	$id = isset($_GET["id"]) ? str_clean(($_GET["id"]),1) : 0;

	if ($webdms == "web")
		$sql = "SELECT * FROM $t_log_web2 WHERE log_id='$id'";
	else
		$sql = "SELECT * FROM $t_log_dms WHERE log_id='$id'";
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$arr['nfields'] = mysql_num_fields($sql);
	$row = mysql_fetch_assoc($sql);
	
	for($j=0;$j<$arr['nfields'];$j++)
	{
		$arr[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
	}

	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr), true)); echo "</pre>";

?>