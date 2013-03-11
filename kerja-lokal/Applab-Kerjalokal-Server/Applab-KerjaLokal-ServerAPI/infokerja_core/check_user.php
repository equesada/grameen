<?php

	require "conf.php";
	require "func.php";

	$username = isset($_GET["username"]) ? str_clean(strtolower($_GET["username"])) : "";
	$password = isset($_GET["password"]) ? str_clean($_GET["password"]) : "";

	$arr = array();
	
	$sql = "SELECT * FROM $t_admin WHERE username='$username' AND password='$password'";
	//die($sql);
	
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	
	if (mysql_num_rows($sql) > 0)
	{
		$r = mysql_fetch_assoc($sql);
		$arr["userlevel"] = $r["userlevel"];
		$arr["userid"] = $r["id"];
		$arr["comp_id"] = 0;
		$sql = "SELECT * FROM $t_job_posters WHERE username='$username'";
		$sql = mysql_query($sql) OR die(output(mysql_error()));
		if (mysql_num_rows($sql) > 0)
		{
			$r = mysql_fetch_assoc($sql);
			$arr["jobposter_id"] = $r["jobposter_id"];
		}
	}
	else
	{
		$sql = "SELECT * FROM $t_job_posters WHERE username='$username' AND password='$password'";
		//die($sql);
		$sql = mysql_query($sql) OR die(output(mysql_error()));

		if (mysql_num_rows($sql) > 0)
		{
			$r = mysql_fetch_assoc($sql);
			$arr["userlevel"] = $r["userlevel"];
			$arr["userid"] = $r["jobposter_id"];
			$arr["comp_id"] = $r["comp_id"];
			$arr["jobposter_id"] = $r["jobposter_id"];
		}
		else
		{
			$arr["userlevel"] = 0;
			$arr["userid"] = 0;
			$arr["comp_id"] = 0;
			$arr["jobposter_id"] = 0;
		}
	}
	echo output($arr);

?>