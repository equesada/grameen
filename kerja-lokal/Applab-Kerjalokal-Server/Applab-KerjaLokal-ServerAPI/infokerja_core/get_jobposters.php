<?php
	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$ndata = isset($_GET["ndata"]) ? str_clean(strtoupper($_GET["ndata"])) : 0;
	$page = isset($_GET["page"]) ? str_clean(strtoupper($_GET["page"])) : 0;
	$order = isset($_GET["order"]) ? $t_job_posters.".".str_clean(($_GET["order"])) : "company_name";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean(strtoupper($_GET["ascdesc"])) : "asc";
	$callback = isset($_GET['callback']);

	$status = isset($_GET["status"]) ? ($_GET["status"] == "0" ? "0" : str_clean($_GET["status"])) : "";
	$username = isset($_GET["username"]) ? ($_GET["username"] == "_" ? "" : str_clean($_GET["username"])) : "";
	$userlevel = isset($_GET["userlevel"]) ? ($_GET["userlevel"] == "0" ? "0" : str_clean($_GET["userlevel"])) : 0;
	$email = isset($_GET["email"]) ? ($_GET["email"] == "_" ? "" : str_clean($_GET["email"])) : "";
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
		case "comp_id" : $order = "$t_job_posters.comp_id"; break;
		case "status" : $order = "$t_job_posters.status"; break;
		case "date_add" : $order = "$t_job_posters.date_add"; break;
	}
	
	$search = "";
	if (($status != "0") && ($status != "")) $search .= "AND $t_job_posters.status LIKE \"%$status%\"";
	if ($username != "") $search .= "AND $t_job_posters.username LIKE \"%$username%\"";
	if (($userlevel != "0") && ($userlevel != "")) $search .= "AND $t_job_posters.userlevel LIKE \"%$userlevel%\"";
	if ($email != "") $search .= "AND $t_job_posters.email LIKE \"%$email%\"";
	if ($date_add != "") $search .= "AND $t_job_posters.date_add LIKE \"%$date_add%\"";
	if ($search != "") $search = substr($search,4);
	
	$sql = "SELECT * FROM $t_job_posters WHERE country_id='$country_id'";
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$arr["totaldata"] = mysql_num_rows($sql);
	$arr['ndata'] = $ndata == "0" ? $arr["totaldata"] : $ndata;

	$sql = "SELECT *, $t_job_posters.username AS jobposter_name, `job_posters`.`email` AS email, $t_companies.name AS company_name, $t_job_posters.status AS jobposter_status";
	$sql .= " FROM ($t_job_posters LEFT JOIN $t_status ON $t_job_posters.status = $t_status.status_id)";
	$sql .= " LEFT JOIN $t_companies ON $t_job_posters.comp_id = $t_companies.comp_id WHERE $t_job_posters.country_id='$country_id'";
	//die($sql);
	$sql = getPagingQuery($sql,$ndata,$page,$order,$ascdesc);
	$arr['pagingLink'] = getPagingLink($sql, $arr['ndata'], $page);
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	
	$arr['nrows'] = mysql_num_rows($sql);
	$arr['nfields'] = mysql_num_fields($sql);
	$arr['npage'] = 1;
	if($arr['nrows'] > 0)
	{
		$arr['npage'] = ceil ($arr["totaldata"] / $arr['nrows']);
	}
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
//echo "<pre>"; print_r(json_decode(output($arr))); echo "</pre>";
?>