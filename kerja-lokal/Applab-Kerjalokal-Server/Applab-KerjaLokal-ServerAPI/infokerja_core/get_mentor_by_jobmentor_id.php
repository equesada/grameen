<?php
	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"],1) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"],1) : 1;
	$order = isset($_GET["order"]) ? str_clean(strtoupper($_GET["order"])) : "title";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean(strtoupper($_GET["ascdesc"])) : "ASC";
	$callback = isset($_GET['callback']);
	$mentor_id = isset($_GET["mentor_id"]) ? str_clean(($_GET["mentor_id"]),1) : 0;


	$sql = "SELECT *, $t_mentors.mentor_id AS mentor_id FROM $t_mentors INNER JOIN $t_subscribers ON $t_mentors.subscriber_id = $t_subscribers.subscriber_id 
		WHERE $t_mentors.country_id='$country_id' AND $t_mentors.mentor_id='$mentor_id'";
	//echo $sql;
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