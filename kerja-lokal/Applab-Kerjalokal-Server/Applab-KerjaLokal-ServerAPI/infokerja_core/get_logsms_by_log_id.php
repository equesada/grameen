<?php

	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$log_id = isset($_GET["log_id"]) ? str_clean(($_GET["log_id"]),1) : 0;


	$sql = "SELECT * FROM $t_log_sms WHERE log_id='$log_id'";
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$arr["nfields"] = mysql_num_fields($sql);
	$arr["totaldata"] = mysql_num_rows($sql); 
	$arr["results"] = array();
	$temp = array();
	while ($row = mysql_fetch_assoc($sql))
	{
		for($j=0;$j<$arr['nfields'];$j++)
		{
			$temp[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
		}
		//array_push($arr["results"], $temp);
	}
	
	echo output($temp);
?>