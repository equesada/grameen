<?php

	require "conf.php";
	require "func.php";

	
    $rel_id = isset($_GET["rel_id"]) ? str_clean(strtoupper($_GET["rel_id"])) : "";
	$jobcat_key = isset($_GET["jobcat_key"]) ? str_clean(($_GET["jobcat_key"])) : "";


	$sql = "SELECT * FROM $t_rel_subscriber_cat WHERE rel_id='$rel_id' AND jobcat_key='$jobcat_key'";
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