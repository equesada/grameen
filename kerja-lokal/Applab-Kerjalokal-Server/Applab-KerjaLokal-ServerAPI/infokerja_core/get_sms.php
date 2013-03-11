<?php

	require "conf.php";
	require "func.php";
	
	$message = isset($_GET["message"]) ? str_clean($_GET["message"]) : 0;
	$msisdn = isset($_GET["msisdn"]) ? str_clean($_GET["msisdn"],1) : "1";
	
	//$msisdn = substr($msisdn,1);
	$row_msisdn = '+'.$msisdn;
	//echo $msisdn."<hr>";
		
	
	$sql = "SELECT * FROM smsg.sms_received WHERE msisdn='$row_msisdn' AND message='$message'";
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
	

?>