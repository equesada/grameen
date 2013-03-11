<?php

	require "conf.php";
	require "func.php";

	
	$order = isset($_GET["order"]) ? str_clean($_GET["order"]) : "rel_id";

	$sql = "SELECT *, $t_rel_subscriber_cat.date_add AS date_add FROM $t_rel_subscriber_cat
		 INNER JOIN $t_subscribers ON $t_rel_subscriber_cat.subscriber_id = $t_subscribers.subscriber_id
		 WHERE n_jobreceived < 8 ORDER BY $order";

	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));

	$arr['nrows'] = mysql_num_rows($sql);
	$arr['nfields'] = mysql_num_fields($sql);
	$arr["results"] = array();
	$i = 0;
	while($row = mysql_fetch_assoc($sql))
	{
		for($j=0;$j<$arr['nfields'];$j++)
		{
			$val[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
		}
		array_push($arr["results"], $val);
		$i++;
	}
	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr),1)); echo "</pre>";

?>