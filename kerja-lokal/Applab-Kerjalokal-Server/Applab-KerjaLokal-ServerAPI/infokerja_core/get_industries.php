<?php

	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"],1) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"],1) : 1;
	//$order = isset($_GET["order"]) ? str_clean(strtoupper($_GET["order"])) : "title";
	if($_GET["order"] == "industry_title" OR $_GET["order"] == "status") $d = ""; else $d = $t_industry.".";
	$order = isset($_GET["order"]) ? $d.str_clean($_GET["order"]) : "industry_title";
	
	
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean(strtoupper($_GET["ascdesc"])) : "ASC";
	$callback = isset($_GET['callback']);
	
	$industry_title = isset($_GET["industry_title"]) ? ($_GET["industry_title"] == "_" ? "" : str_clean($_GET["industry_title"])) : "";
	$description = isset($_GET["description"]) ? ($_GET["description"] == "_" ? "" : str_clean($_GET["description"])) : "";
	$status = isset($_GET["status"]) ? ($_GET["status"] == "0" ? "0" : str_clean($_GET["status"])) : "";
	$date_add = isset($_GET["date_add"]) ? ($_GET["date_add"] == "_" ? "" : str_clean($_GET["date_add"])) : "";
	$date_update = isset($_GET["date_update"]) ? ($_GET["date_update"] == "_" ? "" : str_clean($_GET["date_update"])) : "";
	
	
	
	switch ($order)
	{ 
		//case "industry_id" : $order = "$t_industry.industry_id"; break;
		case "industry_title" : $order = "$t_industry.industry_title"; break;
		case "description" : $order = "$t_industry.description"; break;		
		case "status" : $order = "$t_industry.status"; break;		
		case "date_add" : $order = "$t_industry.date_add"; break;
		case "date_update" : $order = "$t_industry.date_update"; break;
	}
	
	
	$sql = "SELECT * FROM $t_industry WHERE country_id='$country_id'";

	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$arr["totaldata"] = mysql_num_rows($sql);
	$arr['ndata'] = $ndata == "0" ? $arr["totaldata"] : $ndata;
	
	$sql = "SELECT *, $t_status.title AS status_title FROM $t_status INNER JOIN $t_industry ON $t_industry.status = $t_status.status_id";
	//die($sql);
	$sql = getPagingQuery($sql,$ndata,$page,$order,$ascdesc);
	$arr['pagingLink'] = getPagingLink($sql, $arr['ndata'], $page);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	//die($sql);
	$arr['nrows'] = mysql_num_rows($sql);
	$arr['nfields'] = mysql_num_fields($sql);
	$arr['npage'] = ceil ($arr["totaldata"] / $arr['nrows']);
	$arr['page'] = $page;
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