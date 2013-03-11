<?php

	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$comp_id = isset($_GET["comp_id"]) ? str_clean($_GET["comp_id"],1) : "";
	$id = isset($_GET["id"]) ? str_clean($_GET["id"],1) : "";
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"],1) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"],1) : 0;
	$order = isset($_GET["order"]) ? str_clean($_GET["order"]) : "job_title";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean($_GET["ascdesc"]) : "asc";
	$callback = isset($_GET['callback']);
	
	$status = isset($_GET["status"]) ? ($_GET["status"] == "0" ? "0" : str_clean($_GET["status"])) : "";
	$company = isset($_GET["company"]) ? ($_GET["company"] == "_" ? "" : str_clean($_GET["company"])) : "";
	$jobcat_id = isset($_GET["jobcat_id"]) ? ($_GET["jobcat_id"] == "0" ? "0" : str_clean($_GET["jobcat_id"])) : 0;
	$date_add = isset($_GET["date_add"]) ? ($_GET["date_add"] == "_" ? "" : str_clean($_GET["date_add"])) : "";
	$loc_title = isset($_GET["loc_title"]) ? ($_GET["loc_title"] == "_" ? "" : str_clean($_GET["loc_title"])) : "";
	$salary = isset($_GET["salary"]) ? ($_GET["salary"] == "_" ? "" : str_clean($_GET["salary"])) : "";

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
	}
	
	$search = "";
	if (($status != "0") && ($status != "")) $search .= "AND $t_jobs.status LIKE \"%$status%\"";
	if (($comp_id != "0") && ($comp_id != "")) $search .= "AND $t_jobs.comp_id = \"$comp_id\"";
	if ($company != "") $search .= "AND ($t_companies.name LIKE \"%$company%\" OR $t_jobs.comp_name LIKE \"%$company%\") ";
	if (($jobcat_id != "0") && ($jobcat_id != "")) $search .= "AND $t_jobs.jobcat_id LIKE \"%$jobcat_id%\"";
	if ($date_add != "") $search .= "AND $t_jobs.date_add LIKE \"%$date_add%\"";
	//if ($loc_title != "") $search .= "AND $t_location.name LIKE \"%$loc_title%\"";
	if ($salary != "") $search .= "AND salary_min LIKE \"%$salary%\"";
	if ($search != "") $search = substr($search,4);
	
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
//		echo "<pre>"; print_r($temp); echo "</pre><hr>";


		$temp2 = array();
		foreach ($temp as $job)
		{
			//$a = array_search("dki jakarta", array_map('strtolower', $job));
			$a = array_find($loc_title, array_map('strtolower', $job));
			if (($a == "province_name") || ($a == "kotamadya_name") || ($a == "kecamatan_name"))
			{
				//echo $a."<hr>";
				array_push($temp2, $job);
				$b++;
			}
		}

		$temp3 = array();
		foreach($temp2 as $job) $temp3[] = $job[$order];
		array_multisort($temp3, $temp2);
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
	
	
	
	//die();
	//echo $sql."<hr>";
	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr),true)); echo "</pre>";

?>