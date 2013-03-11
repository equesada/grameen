<?php
	require "conf.php";
	require "func.php";

	$id = isset($_GET["id"]) ? str_clean($_GET["id"],1) : 0;
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"],1) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"],1) : 0;
	$order = isset($_GET["order"]) ? str_clean($_GET["order"]) : "name";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean($_GET["ascdesc"]) : "asc";
	$callback = isset($_GET['callback']);
	
	$status = isset($_GET["status"]) ? ($_GET["status"] == "0" ? "0" : str_clean($_GET["status"])) : "";
	$mentor_id = isset($_GET["mentor_id"]) ? ($_GET["mentor_id"] == "0" ? "0" : str_clean($_GET["mentor_id"])) : 0;
	$name = isset($_GET["mentor"]) ? ($_GET["mentor"] == "_" ? "" : str_clean($_GET["mentor"])) : "";
	$mdn = isset($_GET["mdn"]) ? ($_GET["mdn"] == "_" ? "" : str_clean($_GET["mdn"])) : "";
	$pin = isset($_GET["pin"]) ? ($_GET["pin"] == "_" ? "" : str_clean($_GET["pin"])) : "";
	$date_add = isset($_GET["date_add"]) ? ($_GET["date_add"] == "_" ? "" : str_clean($_GET["date_add"])) : "";
	
	
	$arr["totaldata"] =0;
	$arr['ndata'] = 0;
	$arr['pagingLink'] = "";
	$arr['nrows'] = 0;
	$arr['nfields'] = 0;
	$arr['npage'] = 0;
	$arr['page'] = 0;
	$arr['results'] = array();
	
	switch ($order)
	{ 
		case "mentor_id" : $order = "$t_mentors.mentor_id"; break;
		case "status" : $order = "$t_mentors.status"; break;
		case "date_add" : $order = "$t_mentors.date_add"; break;
	}
	
	$search = "";
	if (($status != "0") && ($status != "")) $search .= "AND $t_mentors.status LIKE \"%$status%\"";
	if (($mentor_id != "0") && ($mentor_id != "")) $search .= "AND $t_mentors.mentor_id LIKE \"%$mentor_id%\"";
	if ($name != "") $search .= "AND $t_subscribers.name LIKE \"%$name%\"";
	if ($mdn != "") $search .= "AND $t_mentors.mdn LIKE \"%$mdn%\"";
	if ($pin != "") $search .= "AND $t_mentors.pin LIKE \"%$pin%\"";
	if ($date_add != "") $search .= "AND $t_mentors.date_add LIKE \"%$date_add%\"";
	if ($search != "") $search = substr($search,4);
	
/*	if ($id == 0)	// pertanyakan ID d sini fungsinya buatapa saja
	{
		$sql = "SELECT *, $t_subscribers.subscriber_id AS subscriber_id, $t_mentors.mdn AS mdn, 
		$t_mentors.status AS mentor_status FROM $t_subscribers INNER JOIN $t_mentors ON $t_mentors.subscriber_id = $t_subscribers.subscriber_id";
		if ($search != "") $sql .= " WHERE $search";		
	}
	else
	{ */
		$sql = "SELECT *  FROM $t_mentors WHERE mentor_id='$id'";
		$sql = mysql_query($sql) OR die(output(mysql_error()));
		$r = mysql_fetch_assoc($sql);
		$sql = "SELECT *, $t_subscribers.subscriber_id AS subscriber_id, $t_mentors.mdn AS mdn, 
			$t_mentors.status AS mentor_status FROM $t_subscribers INNER JOIN $t_mentors ON $t_mentors.subscriber_id = $t_subscribers.subscriber_id";
//			if (($filter_key != "") && ($filter_value != "")) $sql .= " AND $filter";
		if ($search != "") $sql .= " WHERE $search";		
//	}
	//echo $search."<br>".$sql."<hr>";
	$q = mysql_query($sql) OR die(output(mysql_error()));
	$arr["totaldata"] = mysql_num_rows($q);
	$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;

	$sql = getPagingQuery($sql,$ndata,$page,$order,$ascdesc);
	$arr['pagingLink'] = getPagingLink($sql, $arr['ndata'], $page);
	$sql = mysql_query($sql) OR die(output(mysql_error()));

	$arr['nrows'] = mysql_num_rows($sql);
	$arr['nfields'] = mysql_num_fields($sql);
	$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
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
	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr),true)); echo "</pre>";

?>