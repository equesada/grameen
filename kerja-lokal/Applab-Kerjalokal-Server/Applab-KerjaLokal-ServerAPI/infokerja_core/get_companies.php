<?php

	require "conf.php";
	require "func.php";

	$loc_id = isset($_GET["loc_id"]) ? ($_GET["loc_id"] == "null" ? '' : str_clean($_GET["loc_id"])) : '';
	
	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$id = isset($_GET["id"]) ? str_clean($_GET["id"],1) : 0;
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"],1) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"],1) : 0;
	$order = isset($_GET["order"]) ? str_clean($_GET["order"]) : "name";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean($_GET["ascdesc"]) : "asc";
	$callback = isset($_GET['callback']);
	
	$status = isset($_GET["status"]) ? ($_GET["status"] == "_" ? "" : str_clean($_GET["status"])) : "";
	$creator_id = isset($_GET["creator_id"]) ? ($_GET["creator_id"] == "_" ? "" : str_clean($_GET["creator_id"])) : "";	
	$industry_id = isset($_GET["industry_id"]) ? ($_GET["industry_id"] == "_" ? "" : str_clean($_GET["industry_id"])) : "";
	$comp_id = isset($_GET["comp_id"]) ? ($_GET["comp_id"] == "_" ? "" : str_clean($_GET["comp_id"])) : "";
	$name = isset($_GET["name"]) ? ($_GET["name"] == "_" ? "" : str_clean($_GET["name"])) : "";	
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
	/*
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
		case "email" : $order = "$t_companies.email"; break;
		default: $order = "$t_companies.comp_id"; break;
	}
	*/
	$search = "";
	if (($status != "0") && ($status != "")) $search .= "AND $t_companies.status = \"$status\"";
	if (($creator_id != "0") && ($creator_id != "")) $search .= "AND $t_companies.creator_id =\"$creator_id\"";
	if (($industry_id != "0") && ($industry_id != "")) $search .= "AND $t_companies.industry_id =\"%$industry_id%\"";
	//if (($comp_id != "0") && ($comp_id != "")) $search .= "AND $t_companies.comp_id = \"$comp_id\"";
	if ($name != "") $search .= "AND $t_companies.name LIKE \"%$name%\"";	
	if ($cp!= "") $search .= "AND $t_companies.cp LIKE \"%$cp%\"";	
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
				INNER JOIN $t_job_posters ON $t_companies.creator_id = $t_job_posters.jobposter_id";
				//LEFT JOIN $t_jobs ON $t_companies.comp_id=$t_jobs.comp_id";  					,SUM(IF($t_jobs.status ='1',1,0)) AS active, SUM(IF($t_jobs.status ='2',1,0)) AS inactive, COUNT($t_jobs.status) AS total
				//	WHERE $t_companies.comp_id = $id";
			if ($search != "") $sql .= " WHERE $search";
			//$sql .= " GROUP BY $t_companies.comp_id";	
		}
	}
	//echo $sql."<hr>";
	//if ($loc_title == "")
	{
	
		//$q = mysql_query($sql) OR die(output(mysql_error()));
		//$arr["totaldata"] = mysql_num_rows($q);
		//$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;
	
		//$sql = getPagingQuery($sql,0,$page,$t_companies.".comp_id",$ascdesc);
		//echo $sql."<hr>";
		$sql = mysql_query($sql) OR die(output(mysql_error()));
	
		//$arr['nrows'] = mysql_num_rows($sql);
		$arr['nfields'] = mysql_num_fields($sql);
		//$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
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
		//add ali	
			$company_id = $val["comp_id"];
			$sql2 = "SELECT SUM(IF(STATUS='1',1,0)) AS active, SUM(IF(STATUS='2',1,0)) AS inactive FROM jobs WHERE comp_id='$company_id'";
			$sql2 = mysql_query($sql2) OR die(output(mysql_error()));
			$row2 = mysql_fetch_assoc($sql2);
			$val["active"] = ($row2["active"])?$row2["active"]:0;
			$val["inactive"] = ($row2["inactive"])?$row2["inactive"]:0;
			$val["total"] = $row2["active"]+$row2["inactive"];		
		//add ali			*/
			array_push($arr["results"], $val);
		}
	} 
	//else 
	{
		//echo "ada<br>$sql<br>";
		//$sql = mysql_query($sql) OR die(output(mysql_error()));
/*		$temp = array();
		$arr['nfields'] = mysql_num_fields($sql);
		while($row = mysql_fetch_assoc($sql))
		{
			for($j=0;$j<$arr['nfields'];$j++)
			{
				$val[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
			}

			$company_id = $val["comp_id"];
			$sql2 = "SELECT SUM(IF(STATUS='1',1,0)) AS active, SUM(IF(STATUS='2',1,0)) AS inactive FROM jobs WHERE comp_id='$company_id'";
			$sql2 = mysql_query($sql2) OR die(output(mysql_error()));
			$row2 = mysql_fetch_assoc($sql2);
			$val["active"] = ($row2["active"])?$row2["active"]:0;
			$val["inactive"] = ($row2["inactive"])?$row2["inactive"]:0;
			$val["total"] = $row2["active"]+$row2["inactive"];		

			//array_push($arr["results"], $val); 
			array_push($temp, $val);
			//echo "<pre>"; print_r($val); echo "</pre><hr>";
		}

		$a = 0;
		foreach ($temp as $job)
		{
			$kecamatan_id = $job["parent_id"];
			//echo $kecamatan_id."<hr>";
			$sql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kecamatan_id'";
			//die($sql);
			$sql = mysql_query($sql) OR die(output(mysql_error()));
			
			$row = mysql_fetch_assoc($sql);
			$temp[$a]["kecamatan_id"] = $kecamatan_id;
			$temp[$a]["kecamatan_name"] = $row["name"];
			
			$kotamadya_id = $row["parent_id"];
			$sql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kotamadya_id'";
			$sql = mysql_query($sql) OR die(output(mysql_error()));
			$row = mysql_fetch_assoc($sql);
			$temp[$a]["kotamadya_id"] = $kotamadya_id;
			$temp[$a]["kotamadya_name"] = $row["name"];
			
			$province_id = $row["parent_id"];
			$sql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$province_id'";
			$sql = mysql_query($sql) OR die(output(mysql_error()));
			$row = mysql_fetch_assoc($sql);
			$temp[$a]["province_id"] = $province_id;
			$temp[$a]["province_name"] = $row["name"];
			$a++;
		}
*/		$b = 0;
		//echo "<pre>"; print_r($arr); echo "</pre><hr>";	
		
		$temp2 = array();
		if ($loc_title != "")
		{
			foreach ($arr["results"] as $job)
			{
				//$a = array_search("dki jakarta", array_map('strtolower', $job));
				//if ($loc_title != "") $a = array_find(strtolower($loc_title), array_map('strtolower', $job), false, "province_name, kotamadya_name, kecamatan_name, loc_title");
				$a = array_find(strtolower($loc_title), array_map('strtolower', $job), false, "province_name, kotamadya_name, kecamatan_name, loc_title");
				//echo $a."<hr>";
				if (($a == "province_name") || ($a == "kotamadya_name") || ($a == "kecamatan_name") || ($a == "loc_title") || ($a != ""))
				{
					//echo $a."<hr>";
					array_push($temp2, $job);
				}
			}
		}
		else
			foreach ($arr["results"] as $job)
			{
					array_push($temp2, $job);
				
			}
		
		//echo "<pre>"; print_r($arr["results"]); echo "</pre><hr>";

		$temp3 = array();
		
		switch ($order)
		{ 
			//case "$t_jobs.comp_name" : $order = "company_name"; break;
			//case "$t_companies.loc_id" : $order = "loc_id"; break;
			case "comp_id" : $order = "comp_id"; break;
			case "status" : $order = "status"; break;
			case "username" : $order = "username"; break;
			case "date_add" : $order = "date_add"; break;
			case "name" : $order = "company_name"; break;
			case "industry_title" : $order = "industry_title"; break;
			case "active" : $order = "active"; break;
			case "inactive" : $order = "inactive"; break;						
			case "total" : $order = "total"; break;
			case "province_name" : $order = "province_name"; break;
			case "kotamadya_name" : $order = "kotamadya_name"; break;
			case "kecamatan_name" : $order = "kecamatan_name"; break;
			case "loc_title" : $order = "loc_title"; break;									
			case "cp" : $order = "cp"; break;			
			case "tel" : $order = "tel"; break;
			case "email" : $order = "email"; break;
			default : $order = "comp_id";
		}
		//echo $order."<hr>";  // di sini data 5 menjadi NOL, knp?
		//foreach($temp2 as $job) $temp3[] = $job[$order];
		foreach($temp2 as $key => $row) { $temp3[$key]  = $row[$order]; }
		if ($ascdesc == "ASC")
			{ array_multisort($temp3, SORT_ASC, $temp2); }
		else
			{ array_multisort($temp3, SORT_DESC, $temp2); }
		$a = 0;

		//echo "<pre>"; print_r($temp3); echo "</pre>";
/*
		$arr["totaldata"] = sizeof($temp2);
		$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;

		//$arr['nrows'] = mysql_num_rows($sql);
		$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
		$arr['page'] = $page;
		if ($page != 0)
			if ($arr['page'] < $arr['npage'])
			{ $arr['nrows'] = $ndata; }
			else
			{ $arr['nrows'] = $arr["totaldata"] % $ndata; }
		else
			$arr['nrows'] = $arr["totaldata"];

		$bottom_array = $ndata * ($page-1);
		
		if ($page == $arr['npage'])
			$upper_array = $bottom_array+($arr["totaldata"] % $ndata);
		else
			$upper_array = $bottom_array + $arr['ndata'];
	//		$upper_array = $bottom_array + $arr['nrows'];
		//echo "<pre>"; print_r($temp2); echo "</pre>";
		//echo $bottom_array."-".$upper_array."<hr>";
		$arr["results"] = array();
		for($a = $bottom_array; $a < $upper_array; $a++)
		{
			array_push($arr["results"], $temp2[$a]);
			//echo $a."<hr>";
		}
		//$arr['results'] = $temp2;		
		$arr['nrows'] = mysql_num_rows($sql);
		$arr['nfields'] = mysql_num_fields($sql);
		$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
		$arr['offset'] = ($page > 0) ? ($page - 1) * $ndata : 0;
		$arr['nopage'] = $page;
		$arr['page'] = $page;
		//$arr['results'] = array();
		//print_r($sql); exit();			
*/
		$arr["totaldata"] = sizeof($temp2);
		$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;
		//$arr['nrows'] = mysql_num_rows($sql);
		$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
		$arr['page'] = $page;
		if ($page != 0)
			if ($arr['page'] < $arr['npage'])
			{ $arr['nrows'] = $ndata; }
			else
			{ $arr['nrows'] = $arr["totaldata"] % $ndata; }
		else
			$arr['nrows'] = $arr["totaldata"];
		$bottom_array = $ndata * ($page-1);
		$upper_array = $bottom_array + $arr['nrows'];
		//echo $bottom_array."-".$upper_array."<hr>";
		$arr["results"] = array();
		for($a = $bottom_array; $a < $upper_array; $a++)
		{
			array_push($arr["results"], $temp2[$a]);
			//echo $a."<hr>";
		}
		//$arr['results'] = $temp2;
		$arr['nrows'] = mysql_num_rows($sql);
		$arr['nfields'] = mysql_num_fields($sql);
		$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
		$arr['offset'] = ($page > 0) ? ($page - 1) * $ndata : 0;
		$arr['nopage'] = $page;
		$arr['page'] = $page;
	}
	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr),true)); echo "</pre>";
	
?>