<?php

	require "conf.php";
	require "func.php";

	$approver_id = isset($_GET["approver_id"]) ? str_clean(strtoupper($_GET["approver_id"])) : "ID";
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"],1) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"],1) : 1;
	$order = isset($_GET["order"]) ? str_clean(strtoupper($_GET["order"])) : "title";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean(strtoupper($_GET["ascdesc"])) : "ASC";
	$callback = isset($_GET['callback']);
	$id = isset($_GET["id"]) ? str_clean(($_GET["id"]),1) : 0;

	$sql = "SELECT status_id,title,country_id,date_add,date_update FROM $t_status WHERE status_id = '$stat_id'";
	
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