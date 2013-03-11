<?php
	require "conf.php";
	require "func.php";
	
	$id = isset($_GET["id"]) ? str_clean($_GET["id"]) : "";
	
	$sql = "SELECT * FROM education WHERE edu_title='$id'";
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