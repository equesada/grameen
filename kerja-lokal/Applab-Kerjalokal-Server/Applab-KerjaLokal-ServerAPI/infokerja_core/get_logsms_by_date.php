<?php

	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$date_send = isset($_GET["date_send"]) ? str_clean(($_GET["date_send"])) : date("Y-m-d");


	$sql = "SELECT * FROM $t_log_sms WHERE DATE_FORMAT(date_send, '%Y-%m-%d') = '$date_send'";
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
		array_push($arr["results"], $temp);
	}
	//echo "<pre>"; print_r($arr); echo "</pre>";
	echo output($arr);
?>