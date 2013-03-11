<?php
	require_once "conf.php";
	require_once "func.php";
	
	$id = isset($_GET["id"]) ? str_clean(($_GET["id"]),1) : 0;

	$sql = "DELETE FROM $t_jobs WHERE job_id='$id'";
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	echo output(1);

?>