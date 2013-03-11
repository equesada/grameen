<?php
	require "conf.php";
	require "func.php";

	$username = isset($_GET["username"]) ? str_clean(($_GET["username"])) : "";
	$sql = "SELECT * FROM $t_job_posters WHERE username='$username' OR email='$username'";
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$arr['nfields'] = mysql_num_fields($sql);
	$row = mysql_fetch_assoc($sql);
	
	for($j=0;$j<$arr['nfields'];$j++)
	{
		$arr[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
	}

	echo output($arr);
?>