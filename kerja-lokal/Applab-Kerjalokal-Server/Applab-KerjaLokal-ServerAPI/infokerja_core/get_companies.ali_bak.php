<?php

	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$id = isset($_GET["id"]) ? str_clean($_GET["id"],1) : 0;
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"],1) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"],1) : 0;
	$order = isset($_GET["order"]) ? str_clean($_GET["order"]) : "name";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean($_GET["ascdesc"]) : "asc";
	$callback = isset($_GET['callback']);
	
	$status = isset($_GET["status"]) ? ($_GET["status"] == "0" ? "0" : str_clean($_GET["status"])) : "";
	$name = isset($_GET["name"]) ? ($_GET["name"] == "_" ? "" : str_clean($_GET["name"])) : "";
	$industry_id = isset($_GET["industry_id"]) ? ($_GET["industry_id"] == "0" ? "0" : str_clean($_GET["industry_id"])) : 0;
	$date_add = isset($_GET["date_add"]) ? ($_GET["date_add"] == "_" ? "" : str_clean($_GET["date_add"])) : "";
	
	$loc_title = isset($_GET["loc_title"]) ? ($_GET["loc_title"] == "_" ? "" : str_clean($_GET["loc_title"])) : "";
	$cp = isset($_GET["cp"]) ? ($_GET["cp"] == "_" ? "" : str_clean($_GET["cp"])) : "";
	$tel = isset($_GET["tel"]) ? ($_GET["tel"] == "_" ? "" : str_clean($_GET["tel"])) : "";
	$email = isset($_GET["email"]) ? ($_GET["email"] == "_" ? "" : str_clean($_GET["email"])) : "";
	
	
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
		case "comp_id" : $order = "$t_companies.comp_id"; break;
		case "name" : $order = "$t_companies.name"; break;
		case "status" : $order = "$t_companies.status"; break;
		case "date_add" : $order = "$t_companies.date_add"; break;
		
		case "loc_title" : $order = "$t_location.name"; break;
		case "cp" : $order = "$t_companies.cp"; break;
		case "tel" : $order = "$t_companies.tel"; break;
		case "email" : $order = "$t_companies.email"; break;
	}
	
	$search = "";
	if (($status != "0") && ($status != "")) $search .= "AND $t_companies.status = \"$status\"";
	if ($name != "") $search .= "AND $t_companies.name LIKE \"%$name%\"";
	if (($industry_id != "0") && ($industry_id != "")) $search .= "AND $t_companies.industry_id LIKE \"%$industry_id%\"";
	if ($date_add != "") $search .= "AND $t_companies.date_add LIKE \"%$date_add%\"";
	if ($search != "") $search = substr($search,4);

	if ($id == 0)	
	{
		$sql = "SELECT *, $t_companies.comp_id AS comp_id, $t_industry.industry_title AS industry_title, 
			$t_companies.name AS company_name, $t_companies.status AS status, $t_companies.date_add AS date_add,
			$t_location.name AS loc_title, $t_companies.email AS email
			FROM ($t_companies INNER JOIN $t_industry ON $t_companies.industry_id=$t_industry.industry_id)
			INNER JOIN $t_location ON $t_location.loc_id = $t_companies.loc_id
			INNER JOIN $t_job_posters ON $t_companies.creator_id = $t_job_posters.jobposter_id";
		if ($search != "") $sql .= " WHERE $search";
	}
	else
	{
		$sql = "SELECT * FROM $t_job_posters WHERE jobposter_id='$id'";
		$sql = mysql_query($sql) OR die(output(mysql_error()));
		$r = mysql_fetch_assoc($sql);
		if ($r["userlevel"] == "company")
		{
			$sql = "SELECT *, $t_companies.comp_id as comp_id, $t_industry.industry_title as industry_title, 
				$t_companies.name as company_name, $t_companies.status AS status, $t_companies.date_add as date_add,
				$t_location.name AS loc_title, $t_companies.email AS email
				FROM ($t_companies INNER JOIN $t_industry ON $t_industry.industry_id=$t_companies.industry_id)
				INNER JOIN $t_location ON $t_location.loc_id = $t_companies.loc_id
				INNER JOIN $t_job_posters ON $t_companies.creator_id = $t_job_posters.jobposter_id
					WHERE $t_companies.comp_id = \"".$r["comp_id"]."\"";
			if ($search != "") $sql .= " WHERE $search";
		}
		else
		{
			$sql = "SELECT *, $t_companies.comp_id as comp_id, $t_industry.industry_title as industry_title, 
				$t_companies.name as company_name, $t_companies.status AS status, $t_companies.date_add as date_add,
				$t_location.name AS loc_title, $t_companies.email AS email
				FROM ($t_companies INNER JOIN $t_industry ON $t_industry.industry_id=$t_companies.industry_id)
				INNER JOIN $t_location ON $t_location.loc_id = $t_companies.loc_id
				INNER JOIN $t_job_posters ON $t_companies.creator_id = $t_job_posters.jobposter_id
					WHERE $t_companies.comp_id = $id";
			if ($search != "") $sql .= " WHERE $search";
		}
	}
	//echo $sql."<hr>";
	$q = mysql_query($sql) OR die(output(mysql_error()));
	$arr["totaldata"] = mysql_num_rows($q);
	$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;

	$sql = getPagingQuery($sql,$ndata,$page,$order,$ascdesc);
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
		$kecamatan_id = substr($val["loc_id"],5);
		$sql2 = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kecamatan_id'";
		$sql2 = mysql_query($sql2) OR die(output(mysql_error()));
		$row2 = mysql_fetch_assoc($sql2);
		$val["kecamatan_id"] = $kecamatan_id;
		$val["kecamatan_name"] = $row2["name"];
		

		$kotamadya_id = $row2["parent_id"];
		$sql2 = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kotamadya_id'";
		$sql2 = mysql_query($sql2) OR die(output(mysql_error()));
		$row2 = mysql_fetch_assoc($sql2);
		$val["kotamadya_id"] = $kotamadya_id;
		$val["kotamadya_name"] = $row2["name"];
		
		$province_id = $row2["parent_id"];
		$sql2 = "SELECT name, parent_id FROM $t_location WHERE loc_id='$province_id'";
		$sql2 = mysql_query($sql2) OR die(output(mysql_error()));
		$row2 = mysql_fetch_assoc($sql2);
		$val["province_id"] = $province_id;
		$val["province_name"] = $row2["name"];
			
		array_push($arr["results"], $val);
	}
	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr),true)); echo "</pre>";

?>