<?php

	require "conf.php";
	require "func.php";

	
	$mdn = isset($_GET["mdn"]) ? str_clean(strtoupper($_GET["mdn"])) : "";
	$status = isset($_GET["status"]) ? str_clean($_GET["status"],1) : "";
	
	
	$sql = "SELECT *, $t_rel_subscriber_cat.subscriber_id AS subscriber_id 
			FROM $t_rel_subscriber_cat INNER JOIN $t_subscribers ON $t_rel_subscriber_cat.subscriber_id = $t_subscribers.subscriber_id WHERE  $t_subscribers.mdn='$mdn' and $t_rel_subscriber_cat.status ='$status'";
	
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
	//echo "<pre>"; print_r(json_decode(output($arr),true)); echo "</pre>";

?>