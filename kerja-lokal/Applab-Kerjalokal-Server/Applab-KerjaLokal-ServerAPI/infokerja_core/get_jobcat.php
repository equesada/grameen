<?php

	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$key = isset($_GET["key"]) ? str_clean($_GET["key"]) : "";
	$value = isset($_GET["value"]) ? str_clean($_GET["value"]) : "";
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"]) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"]) : 1;
	$order = isset($_GET["order"]) ? str_clean(strtoupper($_GET["order"])) : "jobcat_title";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean(strtoupper($_GET["ascdesc"])) : "ASC";

	$sql = "SELECT * FROM $t_jobs_category";
	
	if ($key != "") 
	{
		$sql .= " WHERE ";
		$key = explode("|",$key);
		$value = explode("|",$value);
		$a = 0;
		foreach ($key as $key2)
		{ 
			$sql .= $key2." = \"$value[$a]\""; 
			$a++;
			if ($a < sizeof($key)) $sql .= " AND ";
		}
	}

	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));


	$arr["totaldata"] = mysql_num_rows($sql);
	$arr['nrows'] = mysql_num_rows($sql);
	$arr['nfields'] = mysql_num_fields($sql);
	$arr['results'] = array();
	
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