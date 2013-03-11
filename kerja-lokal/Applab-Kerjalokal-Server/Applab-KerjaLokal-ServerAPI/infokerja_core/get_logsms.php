<?php

	require "conf.php";
	require "func.php";
	
	list($date_from, $date_to) = explode(":", $_GET["date"]);
	
	$date_from = isset($date_from) ? ($date_from == "null" ? '' : ($date_from == "_" ? '' : str_clean($date_from))) : '';
	//echo $date_from."<hr>";
	$date_to = isset($date_to) ? ($date_to == "null" ? '' : ($date_to == "_" ? '' : str_clean($date_to))) : '';	
	//echo $date_to."<hr>";
	
	
	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"],1) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"],1) : 1;
	$order = isset($_GET["order"]) ? str_clean($_GET["order"]) : "date";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean(strtoupper($_GET["ascdesc"])) : "DESC";
	$callback = isset($_GET['callback']);

	$arr["totaldata"] =0;
	$arr['ndata'] = 0;
	$arr['nrows'] = 0;
	$arr['nfields'] = 0;
	$arr['npage'] = 0;
	$arr['page'] = 0;
	$arr['results'] = array();
	
	
	$search = "";
	if (!empty($date_from)) $search .= " where $t_log_sms.date_send >= '$date_from 00:00:01'  AND $t_log_sms.date_send <= '$date_to 23:59:59' ";
	
	if($search == ""){
		$sql = "SELECT COUNT(*) AS ndata, status, DATE_FORMAT(date_send, '%Y-%m-%d') AS date FROM $t_log_sms 
		GROUP BY DATE_FORMAT(date_send, '%Y-%m-%d'), status ORDER BY $order $ascdesc";	
	}else{
		$sql = "SELECT COUNT(*) AS ndata, status, DATE_FORMAT(date_send, '%Y-%m-%d') AS date FROM $t_log_sms $search
		GROUP BY DATE_FORMAT(date_send, '%Y-%m-%d'), status ORDER BY $order $ascdesc";	
	}
	//if ($search != "") $sql .= " $search"; 
	//echo $sql."<hr>";	
	$sql = mysql_query($sql) OR die(output(mysql_error()));

	$data = array();
	while($row = mysql_fetch_assoc($sql))
	{		
		$data[$row["date"]]["n_sms"] = isSet($data[$row["date"]]["n_sms"]) ? $data[$row["date"]]["n_sms"] : 0;
		$data[$row["date"]]["n_dms"] = isSet($data[$row["date"]]["n_dms"]) ? $data[$row["date"]]["n_dms"] : 0;
		if ($row["status"] == "1") $data[$row["date"]]["n_sms"] = $row["ndata"];
		else if ($row["status"] == "2") $data[$row["date"]]["n_dms"] = $row["ndata"];
	}
	//echo "<pre>"; print_r($data); echo "</pre>";
	//die();
	$arr['results'] = array();
	$b = 1;
	foreach($data as $key => $value)
	{
		$arr['results'][$b]["date"] = $key;
		$arr['results'][$b]["n_sms"] = $value["n_sms"];
		$arr['results'][$b]["n_dms"] = $value["n_dms"];
		$b++;
	}
	//echo "<pre>"; print_r($a); echo "</pre>";
	$arr["totaldata"] = sizeof($data);
	$ndata == "0" ? $arr["totaldata"] : $ndata;
	$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
	$arr['nfields'] = 3;
	
	if ($page <= $arr['npage'])
	{	
		//if ()
		$arr['nrows'] = mysql_num_rows($sql);
	}
	if ($ndata > 0)
	{
		
	}
	else
	{
	
	}
	$arr['nrows'] = mysql_num_rows($sql);
	$arr['page'] = $page;
	
	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr),1)); echo "</pre>";

?>