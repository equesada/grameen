<?php
	require "conf.php";
	require "func.php";
	
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
	$date_send1 = isset($_GET["date_send1"]) ? ($_GET["date_send1"] == "_" ? "" : str_clean($_GET["date_send1"])) : "";
	$sent_time1 = isset($_GET["sent_time1"]) ? ($_GET["sent_time1"] == "_" ? "" : str_clean($_GET["sent_time1"])) : "";
	$jobcat_id = isset($_GET["jobcat_id"]) ? ($_GET["jobcat_id"] == "_" ? "" : str_clean($_GET["jobcat_id"])) : "";
	$subscriber_id = isset($_GET["subscriber_id"]) ? ($_GET["subscriber_id"] == "_" ? "" : str_clean($_GET["subscriber_id"])) : "";
	$mdn = isset($_GET["mdn"]) ? ($_GET["mdn"] == "_" ? "" : str_clean($_GET["mdn"])) : "";
	$jobseeker_name = isset($_GET["jobseeker_name"]) ? ($_GET["jobseeker_name"] == "_" ? "" : str_clean($_GET["jobseeker_name"])) : "";
	$jobseeker_kotamadya = isset($_GET["jobseeker_kotamadya"]) ? ($_GET["jobseeker_kotamadya"] == "_" ? "" : str_clean($_GET["jobseeker_kotamadya"])) : "";
	$job_id = isset($_GET["job_id"]) ? ($_GET["job_id"] == "_" ? "" : str_clean($_GET["job_id"])) : "";
	$title = isset($_GET["title"]) ? ($_GET["title"] == "_" ? "" : str_clean($_GET["title"])) : "";
	$jobtype_id = isset($_GET["jobtype_id"]) ? ($_GET["jobtype_id"] == "_" ? "" : str_clean($_GET["jobtype_id"])) : "";
	$job_kotamadya = isset($_GET["job_kotamadya"]) ? ($_GET["job_kotamadya"] == "_" ? "" : str_clean($_GET["job_kotamadya"])) : "";
	$company_name = isset($_GET["company_name"]) ? ($_GET["company_name"] == "_" ? "" : str_clean($_GET["company_name"])) : "";
	$jobseeker_distance = isset($_GET["jobseeker_distance"]) ? ($_GET["jobseeker_distance"] == "_" ? "" : str_clean($_GET["jobseeker_distance"])) : "";
	$date_send2 = isset($_GET["date_send2"]) ? ($_GET["date_send2"] == "_" ? "" : str_clean($_GET["date_send2"])) : "";
	$sent_time2 = isset($_GET["sent_time2"]) ? ($_GET["sent_time2"] == "_" ? "" : str_clean($_GET["sent_time2"])) : "";
	$status = isset($_GET["status"]) ? ($_GET["status"] == "0" ? "0" : str_clean($_GET["status"])) : "";
	
	switch ($order)
	{ 
		case "jobsend_id" : $order = "$t_jobs_send.jobsend_id"; break;
		case "date_send1" : $order = "$t_log_sms.date_send"; break;
		case "sent_time1" : $order = "sent_time1"; break;
		case "jobcat_id" : $order = "jobcat_id"; break;
		case "subscriber_id" : $order = "subscriber_id"; break;
		case "mdn" : $order = "mdn"; break;
		case "jobseeker_name" : $order = "jobseeker_name"; break;
		case "jobseeker_kotamadya" : $order = "jobseeker_kotamadya"; break;
		case "job_id" : $order = "job_id"; break;
		case "title" : $order = "title"; break;
		case "jobtype_id" : $order = "jobtype_id"; break;
		case "job_kotamadya" : $order = "job_kotamadya"; break;
		case "company_name" : $order = "company_name"; break;
		case "jobseeker_distance" : $order = "jobseeker_distance"; break;
		case "date_send2" : $order = "date_send2"; break;
		case "sent_time2" : $order = "sent_time2"; break;
		
	}
	
	// total rows
	$sql = "
	SELECT 
	jobs_send.jobsend_id,
	date(log_sms.date_send) AS date_send1,
	DATE_FORMAT(log_sms.date_send,'%h:%i:%s %p') AS sent_time1,
	rel_subscriber_cat.rel_id,
	rel_subscriber_cat.jobcat_id,
	rel_subscriber_cat.jobcat_key,
	subscribers.subscriber_id,
	subscribers.mdn,
	subscribers.`name` AS jobseeker_name,
	location.`name` AS jobseeker_kotamadya,
	jobs.job_id,
	jobs.title,
	jobs.jobtype_id,
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

	WHERE jobs_send.`status` = '2'
	
	
	";
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$arr["totaldata"] = mysql_num_rows($sql);
	$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;
	
	$sql = "
	SELECT 
	jobs_send.jobsend_id,
	date(log_sms.date_send) AS date_send1,
	DATE_FORMAT(log_sms.date_send,'%h:%i:%s %p') AS sent_time1,
	rel_subscriber_cat.rel_id,
	rel_subscriber_cat.jobcat_id,
	rel_subscriber_cat.jobcat_key,
	subscribers.subscriber_id,
	subscribers.mdn,
	subscribers.`name` AS jobseeker_name,
	location.`name` AS jobseeker_kotamadya,
	jobs.job_id,
	jobs.title,
	jobs.jobtype_id,
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

	WHERE jobs_send.`status` = '2'
	
	
	";
	//die($sql);
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
		array_push($arr["results"], $val);
	}

	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr),true)); echo "</pre>";
?>