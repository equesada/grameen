<?php

	require "conf.php";
	require "func.php";

	$key = isset($_GET["key"]) ? str_clean($_GET["key"]) : "";
	$value = isset($_GET["value"]) ? str_clean($_GET["value"]) : "";
	$groupby = isset($_GET["groupby"]) ? str_clean($_GET["groupby"]) : "";
	$curr_date = isset($_GET["curr_date"]) ? str_clean($_GET["curr_date"]) : date("Y-m-d");
	$order = isset($_GET["order"]) ? str_clean($_GET["order"]) : "$t_subscribers.subscriber_id";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean($_GET["ascdesc"]) : "ASC";

	$sql = "SELECT $t_subscribers.mdn, $t_subscribers.subscriber_id, jobcat_key, date_expired, $t_subscribers.name ".
		"FROM $t_rel_subscriber_cat INNER JOIN $t_subscribers ON $t_rel_subscriber_cat.subscriber_id = $t_subscribers.subscriber_id ";

	if ($key != "") 
	{
		$sql .= " WHERE ";
		$key = explode("|",$key);
		$value = explode("|",$value);
		$a = 0;
		foreach ($key as $key2)
		{ 
			switch ($key2)
			{
				case "hari_reminder" : $sql .= "TIMESTAMPDIFF(DAY, \"$curr_date\", $t_rel_subscriber_cat.date_expired) = $value[$a]"; break;
				case "rel_status" : $sql .= "$t_rel_subscriber_cat.status='$value[$a]'"; break;
				default : $sql .= $key2." = \"$value[$a]\""; break;
			}
			$a++;
			if ($a < sizeof($key)) $sql .= " AND ";
		}
	}

	if ($groupby == "subscriber_id") $sql .= " GROUP BY $t_subscribers.mdn ";
	$sql = getPagingQuery($sql,$ndata,$page,$order,$ascdesc);

	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	
	$arr["totaldata"] = mysql_num_rows($sql);
	$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;

	$arr['nrows'] = mysql_num_rows($sql);
	$arr['nfields'] = mysql_num_fields($sql);
	$arr['npage'] = 1;
	$arr['page'] = 1;
	$arr['results'] = array();

	//$i = 0;
	while($row = mysql_fetch_assoc($sql))
	{
		for($j=0;$j<$arr['nfields'];$j++)
		{
			$val[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
		}
		array_push($arr["results"], $val);
		//$i++;
	}
	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr), true)); echo "</pre>";

?>