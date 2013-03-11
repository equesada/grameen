<?php
	require "conf.php";
	require "func.php";
	
	list($date_send1_from, $date_send1_to) = explode(":", $_GET["date_send1"]);
	list($date_send2_from, $date_send2_to) = explode(":", $_GET["date_send2"]);
	
	
	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"],1) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"],1) : 0;
	$order = isset($_GET["order"]) ? str_clean($_GET["order"]) : "jobsend_id";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean($_GET["ascdesc"]) : "asc";
	
	$arr["totaldata"] =0;
	$arr['ndata'] = 0;
	$arr['pagingLink'] = "";
	$arr['nrows'] = 0;
	$arr['nfields'] = 0;
	$arr['npage'] = 0;
	$arr['page'] = 0;
	$arr['results'] = array();
	
	$jobsend_id = isset($_GET["jobsend_id"]) ? ($_GET["jobsend_id"] == "_" ? "" : str_clean($_GET["jobsend_id"])) : "";
	$rel_id = isset($_GET["rel_id"]) ? ($_GET["rel_id"] == "_" ? "" : str_clean($_GET["rel_id"])) : "";
	$date_send1 = isset($_GET["date_send1"]) ? ($_GET["date_send1"] == "_" ? "" : str_clean($_GET["date_send1"])) : "";
	$sent_time1 = isset($_GET["sent_time1"]) ? ($_GET["sent_time1"] == "_" ? "" : str_clean($_GET["sent_time1"])) : "";
	//$jobcat_id = isset($_GET["jobcat_id"]) ? ($_GET["jobcat_id"] == "_" ? "" : str_clean($_GET["jobcat_id"])) : "";
	$subscriber_id = isset($_GET["subscriber_id"]) ? ($_GET["subscriber_id"] == "_" ? "" : str_clean($_GET["subscriber_id"])) : "";	
	$jobseeker_name = isset($_GET["jobseeker_name"]) ? ($_GET["jobseeker_name"] == "_" ? "" : str_clean($_GET["jobseeker_name"])) : "";
	$loc_title = isset($_GET["loc_title"]) ? ($_GET["loc_title"] == "_" ? "" : str_clean($_GET["loc_title"])) : "";
	$job_id = isset($_GET["job_id"]) ? ($_GET["job_id"] == "_" ? "" : str_clean($_GET["job_id"])) : "";
	$title = isset($_GET["title"]) ? ($_GET["title"] == "_" ? "" : str_clean($_GET["title"])) : "";
	$jobtype_id = isset($_GET["jobtype_id"]) ? ($_GET["jobtype_id"] == "_" ? "" : str_clean($_GET["jobtype_id"])) : "";
	$job_kotamadya = isset($_GET["job_kotamadya"]) ? ($_GET["job_kotamadya"] == "_" ? "" : str_clean($_GET["job_kotamadya"])) : "";
	$company_name = isset($_GET["company_name"]) ? ($_GET["company_name"] == "_" ? "" : str_clean($_GET["company_name"])) : "";
	$jobseeker_distance = isset($_GET["jobseeker_distance"]) ? ($_GET["jobseeker_distance"] == "_" ? "" : str_clean($_GET["jobseeker_distance"])) : "";
	$date_send2 = isset($_GET["date_send2"]) ? ($_GET["date_send2"] == "_" ? "" : str_clean($_GET["date_send2"])) : "";
	$sent_time2 = isset($_GET["sent_time2"]) ? ($_GET["sent_time2"] == "_" ? "" : str_clean($_GET["sent_time2"])) : "";
	
	
	
	$date_send1_from = isset($date_send1_from) ? ($date_send1_from == "null" ? '' : str_clean($date_send1_from)) : '';
	$date_send1_to = isset($date_send1_to) ? ($date_send1_to == "null" ? '' : str_clean($date_send1_to)) : '';	
	$date_send2_from = isset($date_send2_from) ? ($date_send2_from == "null" ? '' : str_clean($date_send2_from)) : '';
	$date_send2_to = isset($date_send2_to) ? ($date_send2_to == "null" ? '' : str_clean($date_send2_to)) : '';
	$status = isset($_GET["status"]) ? ($_GET["status"] == "null" ? 0 : str_clean($_GET["status"])) : '';
	$jobcat_id = isset($_GET["jobcat_id"]) ? ($_GET["jobcat_id"] == "null" ? '' : str_clean($_GET["jobcat_id"])) : '';
	$job_title = isset($_GET["job_title"]) ? ($_GET["job_title"] == "null" ? '' : str_clean($_GET["job_title"])) : '';
	
	$loc_title = isset($_GET["loc_title"]) ? ($_GET["loc_title"] == "null" ? '' : str_clean($_GET["loc_title"])) : '';
	$comp_id = isset($_GET["comp_id"]) ? ($_GET["comp_id"] == "null" ? '' : str_clean($_GET["comp_id"])) : '';
	$mdn = isset($_GET["mdn"]) ? ($_GET["mdn"] == "null" ? '' : str_clean($_GET["mdn"])) : '';
	
	
	
	
	switch ($order)
	{ 
		case "jobsend_id" : $order = "$t_jobs_send.jobsend_id"; break;
		case "rel_id" : $order = "$t_rel_subscriber_cat.rel_id"; break;
		case "job_id" : $order = "$t_jobs.job_id"; break;
		case "date_send1" : $order = "$t_log_sms.date_send"; break;
		case "sent_time1" : $order = "sent_time1"; break;
		case "jobcat_id" : $order = "jobcat_id"; break;		
		case "subscriber_id" : $order = "$t_subscribers.subscriber_id"; break;
		case "mdn" : $order = "$t_subscribers.mdn"; break;
		case "jobseeker_name" : $order = "jobseeker_name"; break;
		case "loc_title" : $order = "loc_title"; break;
		case "title" : $order = "$t_jobs.title"; break;
		case "jobtype_id" : $order = "jobtype_id"; break;
		case "job_kotamadya" : $order = "job_kotamadya"; break;
		case "company_name" : $order = "company_name"; break;
		case "status" : $order = "$t_jobs_send.status"; break;
		case "jobseeker_distance" : $order = "jobseeker_distance"; break;
		case "date_send2" : $order = "date_send2"; break;
		case "sent_time2" : $order = "sent_time2"; break;
		case "dis" : $order = "$t_location.loc_id"; break;
		
	}
	
	$search = "";
	if (!empty($date_send1_from)) $search .= "AND $t_log_sms.date_send BETWEEN '$date_send1_from' AND '$date_send1_to' ";
	if (!empty($date_send2_from)) $search .= "AND $t_log_sms.date_send BETWEEN '$date_send2_from' AND '$date_send2_to' ";
	if (!empty($status) && ($status != "")) $search .= " AND jobs_send.status = $status ";
	if (!empty($jobseeker_name)) $search .= "AND $t_subscribers.name LIKE \"%$jobseeker_name%\" ";
	if (!empty($jobcat_id)) $search .= "AND $t_jobs.jobcat_id = $jobcat_id ";
	if (!empty($comp_id)) $search .= "AND $t_jobs.comp_id = $comp_id ";
	if (!empty($mdn)) $search .= "AND $t_subscribers.mdn LIKE \"%$mdn%\" ";
	
	//echo $search."<hr>";
	
	
	// total rows
	$sql = "
	SELECT *,
	jobs_send.jobsend_id AS jobsend_id,
	date(log_sms.date_send) AS date_send1,
	DATE_FORMAT(log_sms.date_send,'%h:%i:%s %p') AS sent_time1,
	rel_subscriber_cat.rel_id,
	rel_subscriber_cat.jobcat_id,
	rel_subscriber_cat.jobcat_key,
	subscribers.subscriber_id,
	subscribers.mdn,
	subscribers.`name` AS jobseeker_name,
	location.`name` AS loc_title,
	jobs.job_id,
	jobs.title,
	jobs.jobtype_id,
	jobs.description,
	location.`name` AS job_kotamadya,
	location.`loc_id` AS jobseeker_distance,
	companies.`name` AS company_name,
	companies.`comp_id` AS comp_id,
	jobs_send.`status`,
	date(log_sms.date_send) AS date_send2,
	DATE_FORMAT(log_sms.date_send,'%h:%i:%s %p') AS sent_time2
	
	FROM jobs_send

	INNER JOIN log_sms ON jobs_send.jobsend_id=log_sms.jobsend_id
	INNER JOIN rel_subscriber_cat ON jobs_send.rel_id=rel_subscriber_cat.rel_id
	INNER JOIN subscribers ON jobs_send.subscriber_id=subscribers.subscriber_id
	INNER JOIN location ON subscribers.loc_id=location.loc_id
	INNER JOIN jobs ON jobs_send.job_id=jobs.job_id
	INNER JOIN companies ON jobs.comp_id=companies.comp_id 

	
	
	";
	if ($search != "") $sql .= " $search";
	//die($sql);
	
	
	
	
	$sql = "
	SELECT *,
	jobs_send.jobsend_id AS jobsend_id,
	date(log_sms.date_send) AS date_send1,
	DATE_FORMAT(log_sms.date_send,'%h:%i:%s %p') AS sent_time1,
	rel_subscriber_cat.rel_id,
	rel_subscriber_cat.jobcat_id,
	rel_subscriber_cat.jobcat_key,
	subscribers.subscriber_id,
	subscribers.mdn,
	subscribers.`name` AS jobseeker_name,
	location.`name` AS loc_title,
	location.`loc_id` AS jobseeker_distance,
	jobs.job_id,
	jobs.title,
	jobs.jobtype_id,
	jobs.description,
	location.`name` AS job_kotamadya,
	companies.`name` AS company_name,
	companies.`comp_id` AS comp_id,
	jobs_send.`status`,
	date(log_sms.date_send) AS date_send2,
	DATE_FORMAT(log_sms.date_send,'%h:%i:%s %p') AS sent_time2
	
	FROM jobs_send

	INNER JOIN log_sms ON jobs_send.jobsend_id=log_sms.jobsend_id
	INNER JOIN rel_subscriber_cat ON jobs_send.rel_id=rel_subscriber_cat.rel_id
	INNER JOIN subscribers ON jobs_send.subscriber_id=subscribers.subscriber_id
	INNER JOIN location ON subscribers.loc_id=location.loc_id
	INNER JOIN jobs ON jobs_send.job_id=jobs.job_id
	INNER JOIN companies ON jobs.comp_id=companies.comp_id 

	
	
	";
	if ($search != "") $sql .= " $search";
	
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
		$arr['offset'] = ($page > 0) ? ($page - 1) * $ndata : 0;
		$arr['nopage'] = $page;
		$arr['page'] = $page;
		$arr['results'] = array();

		while($row = mysql_fetch_assoc($sql))
		{
			for($j=0;$j<$arr['nfields'];$j++)
			{
				$val[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
			}
			//array_push($arr["results"], $val);
			//echo "<pre>"; print_r($row); echo "</pre><hr>";
			$subscriber_lat = $row["loc_lat"];
			//echo $subscriber_lat."<hr>";
			$subscriber_lng = $row["loc_lng"];
			//echo $subscriber_lng."<hr>";
			
			$kecamatan_id = substr($val["loc_id"],5);
			$sql2 = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kecamatan_id'";
			$sql2 = mysql_query($sql2) OR die(output(mysql_error()));
			$row2 = mysql_fetch_assoc($sql2);
			
			$val["kecamatan_id"] = $kecamatan_id;
			$val["kecamatan_name"] = $row2["name"];
			$val["pos_lat"] = $row2["loc_lat"];
			$val["pos_lng"] = $row2["loc_lng"];
			

			$kotamadya_id = $row2["parent_id"];
			$sql2 = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kotamadya_id'";
			$sql2 = mysql_query($sql2) OR die(output(mysql_error()));
			$row2 = mysql_fetch_assoc($sql2);
			$val["kotamadya_id"] = $kotamadya_id;
			$val["kotamadya_name"] = $row2["name"];
			$val["pos_lat"] = $row2["loc_lat"];
			$val["pos_lng"] = $row2["loc_lng"];
			
			$province_id = $row2["parent_id"];
			$sql2 = "SELECT name, parent_id FROM $t_location WHERE loc_id='$province_id'";
			$sql2 = mysql_query($sql2) OR die(output(mysql_error()));
			$row2 = mysql_fetch_assoc($sql2);
			$val["province_id"] = $province_id;
			$val["province_name"] = $row2["name"];
			$val["pos_lat"] = $row2["loc_lat"];
			$val["pos_lng"] = $row2["loc_lng"];
			
			$dis = 6371 * (SQRT((($subscriber_lat - $val["pos_lat"])*($subscriber_lat - $val["pos_lat"]))+(($subscriber_lng - $val["pos_lng"])*($subscriber_lng - $val["pos_lng"])))/360);
			$val["dis"] = $dis;
			
			array_push($arr["results"], $val);
			//echo "<pre>"; print_r($val); echo "</pre><hr>";
		}
	}
	else
	{
		//echo "ada<hr>";
		$sql = mysql_query($sql) OR die(output(mysql_error()));
		//echo $sql."<hr>";
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
			//echo "<pre>"; print_r($val); echo "</pre><hr>";
			$subscriber_lat = $row["loc_lat"];
			//echo $subscriber_lat."<hr>";
			$subscriber_lng = $row["loc_lng"];
			//echo $subscriber_lng."<hr>";
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
			$temp[$a]["pos_lat"] = $row["loc_lat"];
			$temp[$a]["pos_lng"] = $row["loc_lng"];
			
			$kotamadya_id = $row["parent_id"];
			$sql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kotamadya_id'";
			$sql = mysql_query($sql) OR die(output(mysql_error()));
			$row = mysql_fetch_assoc($sql);
			$temp[$a]["kotamadya_id"] = $kotamadya_id;
			$temp[$a]["kotamadya_name"] = $row["name"];
			$temp[$a]["pos_lat"] = $row["loc_lat"];
			$temp[$a]["pos_lng"] = $row["loc_lng"];
			
			$province_id = $row["parent_id"];
			$sql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$province_id'";
			$sql = mysql_query($sql) OR die(output(mysql_error()));
			$row = mysql_fetch_assoc($sql);
			$temp[$a]["province_id"] = $province_id;
			$temp[$a]["province_name"] = $row["name"];
			$temp[$a]["pos_lat"] = $row["loc_lat"];
			$temp[$a]["pos_lng"] = $row["loc_lng"];
			
			$dis = 6371 * (SQRT((($subscriber_lat - $val["pos_lat"])*($subscriber_lat - $val["pos_lat"]))+(($subscriber_lng - $val["pos_lng"])*($subscriber_lng - $val["pos_lng"])))/360);
			$temp[$a]["dis"] = $dis;
			
			$a++;
		}
		$b = 0;
		//echo "<pre>"; print_r($temp); echo "</pre><hr>";	
		
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
			//case "province_name" : $order = "province_name"; break;
			//default : $order = $_GET["order"];
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
		$arr['results'] = $temp2;
		
		
		$arr['nrows'] = mysql_num_rows($sql);
		$arr['nfields'] = mysql_num_fields($sql);
		$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
		$arr['offset'] = ($page > 0) ? ($page - 1) * $ndata : 0;
		$arr['nopage'] = $page;
		$arr['page'] = $page;
		//$arr['results'] = array();
		//print_r($sql); exit();	
		//echo $sql."<hr>";
		
	}	

	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr),true)); echo "</pre>";
?>