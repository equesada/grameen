<?php

	require "conf.php";
	require "func.php";

	
    $subscriber_id = isset($_GET["subscriber_id"]) ? str_clean(strtoupper($_GET["subscriber_id"])) : "";
	$jobcat_id = isset($_GET["jobcat_ID"]) ? str_clean(($_GET["jobcat_ID"])) : "";
	$date = isset($_GET["date"]) ? str_clean($_GET["date"]) : "";
	
	//$sql = "SELECT * FROM $t_rel_subscriber_cat WHERE subscriber_id='$subscriber_id' AND jobcat_id='$jobcat_id' AND date_active > '$date_check'";
	$sql = "SELECT * FROM $t_rel_subscriber_cat WHERE subscriber_id='$subscriber_id' AND jobcat_id='$jobcat_id' AND LEFT(DATE_ADD, 10)='$date' AND status=2";
	
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$arr['nfields'] = mysql_num_fields($sql);
	$arr["totaldata"] = mysql_num_rows($sql); 
	$row = mysql_fetch_assoc($sql);
	
	for($j=0;$j<$arr['nfields'];$j++)
	{
		$arr[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
	}

	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr), true)); echo "</pre>";

?>