<?php

	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$id = isset($_GET["id"]) ? str_clean($_GET["id"],1) : 0;
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"],1) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"],1) : 0;
	$order = isset($_GET["order"]) ? str_clean($_GET["order"]) : "job_title";
	$ascdesc = isset($_GET["ascdesc"]) ? strtoupper(str_clean($_GET["ascdesc"])) : "ASC";
	$callback = isset($_GET['callback']);
	
	$status = isset($_GET["status"]) ? ($_GET["status"] == "0" ? "0" : str_clean($_GET["status"])) : "";
	$company = isset($_GET["company"]) ? ($_GET["company"] == "_" ? "" : str_clean($_GET["company"])) : "";
	$jobcat_id = isset($_GET["jobcat_id"]) ? ($_GET["jobcat_id"] == "0" ? "0" : str_clean($_GET["jobcat_id"])) : 0;
	$date_add = isset($_GET["date_add"]) ? ($_GET["date_add"] == "_" ? "" : str_clean($_GET["date_add"])) : "";
	$date_expired = isset($_GET["date_expired"]) ? ($_GET["date_expired"] == "_" ? "" : str_clean($_GET["date_expired"])) : "";
	$salary = isset($_GET["salary"]) ? ($_GET["salary"] == "_" ? "" : str_clean($_GET["salary"])) : "";
	$loc_title = isset($_GET["loc_title"]) ? ($_GET["loc_title"] == "_" ? "" : str_clean($_GET["loc_title"])) : "";

	$arr["totaldata"] =0;
	$arr['ndata'] = 0;
	$arr['nrows'] = 0;
	$arr['nfields'] = 0;
	$arr['npage'] = 0;
	$arr['page'] = 0;
	$arr['results'] = array();
	
	switch ($order)
	{ 
		case "company_name" : $order = "$t_jobs.comp_name"; break;
		case "loc_id" : $order = "$t_companies.loc_id"; break;
		case "status" : $order = "$t_jobs.status"; break;
		case "date_add" : $order = "$t_jobs.date_add"; break;
		default : $order = "job_title"; 
	}
	
	$search = "";
	if (($status != "0") && ($status != "")) $search .= "AND $t_jobs.status = \"$status\"";
	if ($company != "") $search .= "AND ($t_companies.name LIKE \"%$company%\" OR $t_jobs.comp_name LIKE \"%$company%\") ";
	if (($jobcat_id != "0") && ($jobcat_id != "")) $search .= "AND $t_jobs.jobcat_id = \"$jobcat_id\"";
	if ($date_add != "") $search .= "AND $t_jobs.date_add LIKE \"%$date_add%\"";
	if ($date_expired != "") $search .= "AND date_expired > \"$date_expired\"";
	if ($salary != "") $search .= "AND salary_min LIKE \"%$salary%\"";
	
	//if ($loc_title != "") $search .= "AND $t_location.name LIKE \"%$loc_title%\"";
	if ($search != "") $search = substr($search,4);
	/*
	if (($filter_key != "") && ($filter_value != "")) 
		switch ($filter_key)
		{ 
			case "company_name" : 
				$filter = " ($t_companies.name LIKE \"%$filter_value%\") OR ($t_jobs.comp_name LIKE \"%$filter_value%\")"; 
				break;
			case "jobcat_title" : 
				$filter = " $t_jobs_category.jobcat_title LIKE \"%$filter_value%\""; 
				break;
			case "date_add" : 
				$filter = " $t_jobs.date_add LIKE \"%$filter_value%\""; 
				break;
			case "status" : 
				$filter = " $t_status.title LIKE \"%$filter_value%\""; 
				break;
			case "salary_gt" : $filter = "$t_jobs.salary_min > $filter_value"; break;
			case "salary_lt" : $filter = "$t_jobs.salary_max < $filter_value"; break;
			case "loc_name" : $filter = "$t_location.name LIKE \"%$filter_value%\""; break;
		}
	*/
	
	if ($id == 0)	
	{
		$sql = "SELECT *, $t_jobs.title AS job_title, $t_jobs.description AS job_desc, $t_jobs.status AS job_status, 
			$t_status.title AS jobstatus_title, $t_jobs.loc_id AS loc_id, $t_location.name AS loc_title, $t_jobs.comp_id AS comp_id, 
			$t_companies.name AS company_name, $t_companies.description AS comp_desc, $t_jobs.date_add AS date_add 
			FROM ($t_jobs INNER JOIN $t_job_posters ON $t_jobs.jobposter_id=$t_job_posters.jobposter_id)
			INNER JOIN $t_status ON $t_jobs.status = $t_status.status_id
			INNER JOIN $t_location ON $t_jobs.loc_id = $t_location.loc_id
			INNER JOIN $t_jobs_category ON $t_jobs_category.jobcat_id = $t_jobs.jobcat_id
			INNER JOIN $t_companies ON $t_job_posters.comp_id = $t_companies.comp_id
			INNER JOIN $t_industry ON $t_companies.industry_id = $t_industry.industry_id";
		if ($search != "") $sql .= " WHERE $search";
	}
	else
	{
		$sql = "SELECT * FROM $t_job_posters WHERE jobposter_id='$id'";
		$sql = mysql_query($sql) OR die(output(mysql_error()));
		$r = mysql_fetch_assoc($sql);
		if ($r["userlevel"] == "company")
		{
			$sql = "SELECT *, $t_jobs.title as job_title, $t_jobs.description AS job_desc, $t_jobs.status AS job_status, 
				$t_status.title AS jobstatus_title, $t_jobs.loc_id AS loc_id, $t_location.name AS loc_title,
				$t_companies.name AS company_name, $t_companies.description AS comp_desc, $t_jobs.date_add AS date_add 
				FROM ($t_jobs INNER JOIN $t_job_posters ON $t_jobs.jobposter_id=$t_job_posters.jobposter_id)
				INNER JOIN $t_status ON $t_jobs.status = $t_status.status_id
				INNER JOIN $t_location ON $t_jobs.loc_id = $t_location.loc_id
				INNER JOIN $t_jobs_category ON $t_jobs_category.jobcat_id = $t_jobs.jobcat_id
				INNER JOIN $t_companies ON $t_job_posters.comp_id = $t_companies.comp_id
				INNER JOIN $t_industry ON $t_companies.industry_id = $t_industry.industry_id
				WHERE $t_companies.comp_id = \"".$r["comp_id"]."\"";
//			if (($filter_key != "") && ($filter_value != "")) $sql .= " AND $filter";
			if ($search != "") $sql .= " WHERE $search";
		}
		else
		{
			$sql = "SELECT *, $t_jobs.title as job_title, $t_jobs.description AS job_desc, $t_jobs.status AS job_status, 
				$t_status.title AS jobstatus_title, $t_jobs.loc_id AS loc_id, $t_location.name AS loc_title,
				$t_companies.name AS company_name, $t_companies.description AS comp_desc, $t_jobs.date_add AS date_add 
				FROM ($t_jobs INNER JOIN $t_job_posters ON $t_jobs.jobposter_id=$t_job_posters.jobposter_id)
				INNER JOIN $t_status ON $t_jobs.status = $t_status.status_id
				INNER JOIN $t_location ON $t_jobs.loc_id = $t_location.loc_id
				INNER JOIN $t_jobs_category ON $t_jobs_category.jobcat_id = $t_jobs.jobcat_id
				INNER JOIN $t_companies ON $t_job_posters.comp_id = $t_companies.comp_id
				INNER JOIN $t_industry ON $t_companies.industry_id = $t_industry.industry_id
				WHERE $t_jobs.jobposter_id = $id";
//			if (($filter_key != "") && ($filter_value != "")) $sql .= " AND $filter";
			if ($search != "") $sql .= " WHERE $search";
		}
	}
	//echo $sql."<hr>";
	

	if ($loc_title == "")
	{
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
			//array_push($arr["results"], $val);
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

			array_push($arr['results'], $val);
		}
	}
	else
	{
		
		$sql = mysql_query($sql) OR die(output(mysql_error()));
		$temp = array();
		$arr['nfields'] = mysql_num_fields($sql);
		while($row = mysql_fetch_assoc($sql))
		{
			for($j=0;$j<$arr['nfields'];$j++)
			{
				$val[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
			}
			//array_push($arr["results"], $val);
			array_push($temp, $val);
		}
		
		$a = 0;
		foreach ($temp as $job)
		{
			$kecamatan_id = $job["parent_id"];
			//echo $kecamatan_id."<hr>";
			$sql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kecamatan_id'";
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
		$b = 0;
		echo "<pre>"; print_r($temp); echo "</pre><hr>";


		$temp2 = array();
		foreach ($temp as $job)
		{
			//$a = array_search("dki jakarta", array_map('strtolower', $job));
			$a = array_find(strtolower($loc_title), array_map('strtolower', $job), false, "province_name, kotamadya_name, kecamatan_name, loc_title");
			//echo $a."<hr>";
			if (($a == "province_name") || ($a == "kotamadya_name") || ($a == "kecamatan_name") || ($a == "loc_title") || ($a != ""))
			{
				//echo $a."<hr>";
				array_push($temp2, $job);
			}
		}
		//echo "<pre>"; print_r($temp2); echo "</pre><hr>";

		$temp3 = array();
		switch ($order)
		{ 
			case "$t_jobs.comp_name" : $order = "company_name"; break;
			case "$t_companies.loc_id" : $order = "loc_id"; break;
			case "$t_jobs.status" : $order = "status"; break;
			case "$t_jobs.date_add" : $order = "date_add"; break;
			default : $order = $_GET["order"];
		}

		//foreach($temp2 as $job) $temp3[] = $job[$order];
		foreach($temp2 as $key => $row) { $temp3[$key]  = $row[$order]; }
		if ($ascdesc == "ASC")
			{ array_multisort($temp3, SORT_ASC, $temp2); }
		else
			{ array_multisort($temp3, SORT_DESC, $temp2); }
		$a = 0;
		
		//echo $b;
		//echo "<pre>"; print_r($temp2); echo "</pre>";

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
		for($a = $bottom_array; $a < $upper_array; $a++)
		{
			array_push($arr["results"], $temp2[$a]);
			//echo $a."<hr>";
		}
		
		//$arr['results'] = $temp2;
		
	}
	//echo output($arr);
	echo "<pre>"; print_r(json_decode(output($arr),true)); echo "</pre>";

?>