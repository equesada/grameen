<?php

	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$rel_id = isset($_GET["rel_id"]) ? str_clean(($_GET["rel_id"]),1) : 0;
    $status = isset($_GET["status"]) ? str_clean($_GET["status"],1) : "";
    $order = isset($_GET["order"]) ? str_clean($_GET["order"]) : "date_send";
    $ascdesc = isset($_GET["ascdesc"]) ? str_clean($_GET["ascdesc"]) : "desc";


	$sql = "SELECT * FROM $t_log_sms WHERE rel_id='$rel_id' AND status='$status' ORDER BY $order $ascdesc";
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
	
	echo output($arr);
?>