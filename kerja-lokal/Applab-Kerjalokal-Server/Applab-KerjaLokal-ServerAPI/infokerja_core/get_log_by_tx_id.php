<?php
	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$ndata = isset($_GET["ndata"]) ? str_clean(strtoupper($_GET["ndata"])) : 0;
	$page = isset($_GET["page"]) ? str_clean(strtoupper($_GET["page"])) : 0;
	$order = isset($_GET["order"]) ? $t_job_posters.".".str_clean(($_GET["order"])) : "";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean(strtoupper($_GET["ascdesc"])) : "asc";

	$webdms = isset($_GET["webdms"]) ? str_clean($_GET["webdms"]) : "web";
	$tx_id = isset($_GET["tx_id"]) ? str_clean($_GET["tx_id"]) : 0;

	if (($webdms == "web") || ($webdms == "dmsweb"))
		$sql = "SELECT * FROM $t_log_web WHERE tx_id='$tx_id'";
	else
		$sql = "SELECT * FROM $t_log_dms WHERE tx_id='$tx_id'";
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$arr["totaldata"] = mysql_num_rows($sql);
	$arr['ndata'] = $ndata == "0" ? $arr["totaldata"] : $ndata;
	
	if ($arr["totaldata"] > 1)
	{
		$arr['nrows'] = mysql_num_rows($sql);
		$arr['nfields'] = mysql_num_fields($sql);
		$arr['npage'] = 1;
		if($arr['nrows'] > 0)
			$arr['npage'] = ceil ($arr["totaldata"] / $arr['nrows']);
		$arr['page'] = $page;
		$arr['results'] = array();
		
		while($row = mysql_fetch_assoc($sql))
		{
			for($j=0;$j<$arr['nfields'];$j++)
			{
				$val[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
			}
			array_push($arr["results"], $val);
		}
	}
	else
	{
		$arr['nfields'] = mysql_num_fields($sql);
		$row = mysql_fetch_assoc($sql);
		for($j=0;$j<$arr['nfields'];$j++)
		{
			$arr[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
		}
	}
	echo json_encode($arr);
	//echo "<pre>"; print_r(json_decode(output($arr), true)); echo "</pre>";

?>