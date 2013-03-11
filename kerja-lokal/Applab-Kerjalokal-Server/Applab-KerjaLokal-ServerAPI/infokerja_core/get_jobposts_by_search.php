<?php

	require "conf.php";
	require "func.php";
	
	list($submit_date_from, $submit_date_to) = explode(":", $_GET["submit_date"]);		
	list($approved_date_from, $approved_date_to) = explode(":", $_GET["approved_date"]);
	$loc_title = isset($_GET["loc_title"]) ? ($_GET["loc_title"] == "null" ? '' : str_clean($_GET["loc_title"])) : '';
	
	$date_expired = isset($_GET["date_expired"]) ? ($_GET["date_expired"] == "_" ? "" : str_clean($_GET["date_expired"])) : "";
	
	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$id = isset($_GET["id"]) ? str_clean($_GET["id"],1) : 0;
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"],1) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"],1) : 0;
	$order = isset($_GET["order"]) ? str_clean($_GET["order"]) : "job_title";
	$ascdesc = isset($_GET["ascdesc"]) ? strtoupper(str_clean($_GET["ascdesc"])) : "ASC";
	$callback = isset($_GET['callback']);
	
	$status = isset($_GET["status"]) ? ($_GET["status"] == "null" ? 0 : str_clean($_GET["status"])) : '';
	//echo $status."<hr>";
	$job_status = isset($_GET["job_status"]) ? ($_GET["job_status"] == "null" ? 0 : str_clean($_GET["job_status"])) : '';
	//echo job_status."<hr>";
	$comp_id = isset($_GET["comp_id"]) ? ($_GET["comp_id"] == "null" ? '' : str_clean($_GET["comp_id"])) : '';
	$jobcat_id = isset($_GET["jobcat_id"]) ? ($_GET["jobcat_id"] == "null" ? '' : str_clean($_GET["jobcat_id"])) : '';
	$job_title = isset($_GET["job_title"]) ? ($_GET["job_title"] == "null" ? '' : str_clean($_GET["job_title"])) : '';
	$approver_id = isset($_GET["approver_id"]) ? ($_GET["approver_id"] == "null" ? '' : str_clean($_GET["approver_id"])) : '';
	$jobposter_id = isset($_GET["jobposter_id"]) ? ($_GET["jobposter_id"] == "null" ? '' : str_clean($_GET["jobposter_id"])) : '';
	//echo $jobposter_id."<hr>";
			
	$submit_date_from = isset($submit_date_from) ? ($submit_date_from == "null" ? '' : str_clean($submit_date_from)) : '';
	$submit_date_to = isset($submit_date_to) ? ($submit_date_to == "null" ? '' : str_clean($submit_date_to)) : '';	
	$approved_date_from = isset($approved_date_from) ? ($approved_date_from == "null" ? '' : str_clean($approved_date_from)) : '';
	$approved_date_to = isset($approved_date_to) ? ($approved_date_to == "null" ? '' : str_clean($approved_date_to)) : '';
	
	$loc_id_provinces_id = isset($loc_id_provinces_id) ? ($loc_id_provinces_id == 'null' ? '' : str_clean($loc_id_provinces_id)) : '';
	$loc_id_kotamadya_id = isset($loc_id_kotamadya_id) ? ($loc_id_kotamadya_id == 'null' ? '' : str_clean($loc_id_kotamadya_id)) : '';
	$loc_id_kecamatan_id = isset($loc_id_kecamatan_id) ? ($loc_id_kecamatan_id == 'null' ? '' : str_clean($loc_id_kecamatan_id)) : '';
	$loc_id_kelurahan_id = isset($loc_id_kelurahan_id) ? ($loc_id_kelurahan_id == 'null' ? '' : str_clean($loc_id_kelurahan_id)) : '';
	
	$arr["totaldata"] =0;
	$arr['ndata'] = 0;
	$arr['nrows'] = 0;
	$arr['nfields'] = 0;
	$arr['npage'] = 0;
	$arr['page'] = 0;
	$arr['results'] = array();

	
	$arr["totaldata"] =0;
	$arr['ndata'] = 0;
	$arr['pagingLink'] = "";
	$arr['nrows'] = 0;
	$arr['nfields'] = 0;
	$arr['npage'] = 0;
	$arr['nopage'] = $page;
	$arr['page'] = 0;
	$arr['results'] = array();
	
	

	$search = "";
	
	if (!empty($status) && ($status != "")) $search .= " AND $t_jobs.status = $status ";	
	if (!empty($submit_date_from)) $search .= "AND $t_jobs.date_add >= '$submit_date_from 00:00:01'  AND $t_jobs.date_add <= '$submit_date_to 23:59:59' ";
	if (!empty($comp_id)) $search .= "AND $t_jobs.comp_id = $comp_id ";
	if (!empty($jobcat_id)) $search .= "AND $t_jobs.jobcat_id = $jobcat_id ";
	if (!empty($comp_id)) $search .= "AND $t_jobs.comp_id = $comp_id ";
	if (!empty($job_title)) $search .= "AND $t_jobs.title LIKE \"%$job_title%\" ";
	if (!empty($jobposter_id)) $search .= "AND $t_jobs.jobposter_id = $jobposter_id ";
	if (!empty($approver_id)) $search .= "AND $t_jobs.approver_id = $approver_id ";
	if (!empty($approved_date_to)) $search .= "AND $t_jobs.approved_date >= '$approved_date_from 00:00:01' AND  $t_jobs.approved_date <= '$approved_date_to 23:59:59' ";
	if (!empty($date_expired)) $search .= "AND date_expired > \"$date_expired\"";
	
	if (!empty($loc_id_provinces_id) && empty($loc_id_kotamadya_id) && empty($loc_id_kecamatan_id) && empty($loc_id_kelurahan_id)) { 
		$search .= "AND $t_jobs.loc_id IN ('$loc_id_provinces_id') "; //$t_location.loc_id = $loc_id_provinces_id
	} else
	if (!empty($loc_id_kotamadya_id) && empty($loc_id_kecamatan_id) && empty($loc_id_kelurahan_id) && empty($loc_id_provinces_id)) {
		$search .= "AND $t_jobs.loc_id IN ('$loc_id_kotamadya_id') ";
	} else
	if (!empty($loc_id_kecamatan_id) && empty($loc_id_kelurahan_id) && empty($loc_id_provinces_id) && empty($loc_id_kotamadya_id)) {
		$search .= "AND $t_jobs.loc_id IN ('$loc_id_kecamatan_id') ";
	} else
	if (!empty($loc_id_kelurahan_id) && empty($loc_id_provinces_id) && empty($loc_id_kotamadya_id) && empty($loc_id_kecamatan_id)) {
		$search .= "AND $t_jobs.loc_id IN ('$loc_id_kelurahan_id') ";
	} else
	if (!empty($loc_id_kelurahan_id) && !empty($loc_id_provinces_id) && !empty($loc_id_kotamadya_id) && !empty($loc_id_kecamatan_id)) {
		$location_id = implode(",", array($loc_id_kelurahan_id,$loc_id_kecamatan_id,$loc_id_kotamadya_id,$loc_id_provinces_id));
		$search .= "AND $t_jobs.loc_id IN ('$location_id') ";
	}
	
	if ($id == 0)	
	{		
		$sql = "SELECT *, $t_jobs.title AS job_title, $t_jobs.description AS job_desc, $t_jobs.status AS job_status, 
			$t_status.title AS jobstatus_title, $t_jobs.loc_id AS loc_id, $t_location.name AS loc_title, $t_jobs.comp_id AS comp_id, 
			$t_companies.name AS company_name, $t_companies.description AS comp_desc, $t_jobs.date_add AS date_add 
			FROM ($t_jobs INNER JOIN $t_job_posters ON $t_jobs.jobposter_id=$t_job_posters.jobposter_id)
			INNER JOIN $t_status ON $t_jobs.status = $t_status.status_id
			INNER JOIN $t_location ON $t_jobs.loc_id = $t_location.loc_id
			INNER JOIN $t_jobs_category ON $t_jobs_category.jobcat_id = $t_jobs.jobcat_id
			INNER JOIN $t_companies ON $t_jobs.comp_id = $t_companies.comp_id
			INNER JOIN $t_industry ON $t_companies.industry_id = $t_industry.industry_id";
		if ($search != "") $sql .= " $search ORDER BY $t_jobs.job_id DESC";
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
				$t_jobs.comp_id AS company_id, $t_companies.name AS company_name, $t_companies.description AS comp_desc, $t_jobs.date_add AS date_add 
				FROM ($t_jobs INNER JOIN $t_job_posters ON $t_jobs.jobposter_id=$t_job_posters.jobposter_id)
				INNER JOIN $t_status ON $t_jobs.status = $t_status.status_id
				INNER JOIN $t_location ON $t_jobs.loc_id = $t_location.loc_id
				INNER JOIN $t_jobs_category ON $t_jobs_category.jobcat_id = $t_jobs.jobcat_id
				INNER JOIN $t_companies ON $t_jobs.comp_id = $t_companies.comp_id
				INNER JOIN $t_industry ON $t_companies.industry_id = $t_industry.industry_id
				WHERE $t_companies.comp_id = \"".$r["comp_id"]."\"";
			//if (($filter_key != "") && ($filter_value != "")) $sql .= " AND $filter";
			if ($search != "") $sql .= " $search ORDER BY $t_jobs.job_id DESC";			
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
				INNER JOIN $t_companies ON $t_jobs.comp_id = $t_companies.comp_id
				INNER JOIN $t_industry ON $t_companies.industry_id = $t_industry.industry_id";	
			if ($search != "") $sql .= " WHERE ".substr($search,4);
			$sql .= " ORDER BY $t_jobs.job_id DESC";			
		}
	}
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

			// add ali, get approver
			$sql2 = mysql_query("SELECT username FROM $t_job_posters WHERE jobposter_id='$val[approver_id]'") OR die(output(mysql_error()));
			$row2 = mysql_fetch_assoc($sql2);
			$val["approver_name"] = $row2["username"];

			array_push($arr['results'], $val);			
		}
		//echo "<pre>"; print_r($arr); echo "</pre><hr>";	
		
		$temp2 = array();
		if ($loc_title != "")
		{
			foreach ($arr["results"] as $job)
			{
				//$a = array_search("dki jakarta", array_map('strtolower', $job));
				$a = array_find(strtolower($loc_title), array_map('strtolower', $job), false, "province_name, kotamadya_name, kecamatan_name, loc_title");
				//echo $a."<hr>";
				if (($a == "province_name") || ($a == "kotamadya_name") || ($a == "kecamatan_name") || ($a == "loc_title") || ($a != ""))
				{ array_push($temp2, $job); }
			}
		}
		else
			foreach ($arr["results"] as $job)
			{ array_push($temp2, $job); }
		//echo "<pre>"; print_r($temp2); echo "</pre><hr>";

		$temp3 = array();					
		switch ($order)
		{ 
			case "$t_jobs.job_id" : $order = "job_id"; break;
			case "$t_jobs.status" : $order = "status"; break;
			case "$t_jobs.date_add" : $order = "date_add"; break;
			case "$t_job_posters.username" : $order = "username"; break;
			case "$t_job_posters.approver_name" : $order = "approver_name"; break;
			case "$t_jobs.approved_date" : $order = "approved_date"; break;
			case "$t_jobs_category.jobcat_title" : $order = "jobcat_title"; break;
			case "$t_location.province_name" : $order = "province_name"; break;
			case "$t_location.kotamadya_name" : $order = "kotamadya_name"; break;
			case "$t_location.kecamatan_name" : $order = "kecamatan_name"; break;
			case "$t_location.loc_title" : $order = "loc_title"; break;
			case "$t_jobs.status" : $order = "status"; break;						
			//default : $order = $_GET["order"];
		}

		//foreach($temp2 as $job) $temp3[] = $job[$order];
		foreach($temp2 as $key => $row) { $temp3[$key]  = $row[$order]; }
		if ($ascdesc == "ASC")
			{ array_multisort($temp3, SORT_ASC, $temp2); }
		else
			{ array_multisort($temp3, SORT_DESC, $temp2); }
		$a = 0;
		

		$arr["totaldata"] = sizeof($temp2);
		$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;		
		$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
		$arr['page'] = $page;
		if ($page != 0)
			if ($arr['page'] < $arr['npage'])
			{ $arr['nrows'] = $ndata; }
			else
			{ $arr['nrows'] = ($arr["totaldata"] % $ndata) == 0 ? $arr['ndata'] : ($arr["totaldata"] % $ndata); }			
		else
			$arr['nrows'] = $arr["totaldata"];
			//$arr['nrows'] = 7;
		$bottom_array = $ndata * ($page-1);
		$upper_array = $bottom_array + $arr['nrows'];
		//echo $bottom_array."-".$upper_array."<hr>";
		
		//unset($arr['results']);
		$arr['results'] = array();
		for($a = $bottom_array; $a < $upper_array; $a++)
		{
			array_push($arr["results"], $temp2[$a]);
			//echo $a."<hr>";
		}
		//$arr['results'] = $temp2;

		$arr['nfields'] = mysql_num_fields($sql);
		$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
		$arr['offset'] = ($page > 0) ? ($page - 1) * $ndata : 0;
		$arr['nopage'] = $page;
		$arr['page'] = $page;
	//}
	echo output($arr)
	//echo "<pre>"; print_r(json_decode(output($arr),true)); echo "</pre>";

?>