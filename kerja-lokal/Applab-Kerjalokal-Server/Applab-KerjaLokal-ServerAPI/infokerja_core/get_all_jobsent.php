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
	//$job_kotamadya = isset($_GET["job_kotamadya"]) ? ($_GET["job_kotamadya"] == "_" ? "" : str_clean($_GET["job_kotamadya"])) : "";
	$company_name = isset($_GET["company_name"]) ? ($_GET["company_name"] == "_" ? "" : str_clean($_GET["company_name"])) : "";
	//$jobseeker_distance = isset($_GET["jobseeker_distance"]) ? ($_GET["jobseeker_distance"] == "_" ? "" : str_clean($_GET["jobseeker_distance"])) : "";
	$date_send2 = isset($_GET["date_send2"]) ? ($_GET["date_send2"] == "_" ? "" : str_clean($_GET["date_send2"])) : "";
	$sent_time2 = isset($_GET["sent_time2"]) ? ($_GET["sent_time2"] == "_" ? "" : str_clean($_GET["sent_time2"])) : "";		
	
	$date_send1_from = isset($date_send1_from) ? ($date_send1_from == "null" ? '' : ($date_send1_from == "_" ? '' : str_clean($date_send1_from))) : '';
	$date_send1_to = isset($date_send1_to) ? ($date_send1_to == "null" ? '' : ($date_send1_to == "_" ? '' : str_clean($date_send1_to))) : '';	
	$date_send2_from = isset($date_send2_from) ? ($date_send2_from == "null" ? '' : ($date_send2_from == "_" ? '' : str_clean($date_send2_from))) : '';
	$date_send2_to = isset($date_send2_to) ? ($date_send2_to == "null" ? '' : ($date_send2_to == "_" ? '' : str_clean($date_send2_to))) : '';
	$status = isset($_GET["status"]) ? ($_GET["status"] == "null" ? 0 : ($_GET["status"] == "_" ? 0 : str_clean($_GET["status"]))) : '';
	$jobcat_id = isset($_GET["jobcat_id"]) ? ($_GET["jobcat_id"] == "null" ? '' : ($_GET["jobcat_id"] == "_" ? '' : str_clean($_GET["jobcat_id"]))) : '';
	$jobcat_id2 = isset($_GET["jobcat_id2"]) ? ($_GET["jobcat_id2"] == "null" ? '' : ($_GET["jobcat_id2"] == "_" ? '' : str_clean($_GET["jobcat_id2"]))) : '';
	$job_title = isset($_GET["job_title"]) ? ($_GET["job_title"] == "null" ? '' : ($_GET["job_title"] == "_" ? '' :str_clean($_GET["job_title"]))) : '';
	
	$loc_title = isset($_GET["loc_title"]) ? ($_GET["loc_title"] == "null" ? '' : ($_GET["loc_title"] == "_" ? '' : str_clean($_GET["loc_title"]))) : '';
	$comp_id = isset($_GET["comp_id"]) ? ($_GET["comp_id"] == "null" ? '' : ($_GET["comp_id"] == "_" ? '' : str_clean($_GET["comp_id"]))) : '';
	$mdn = isset($_GET["mdn"]) ? ($_GET["mdn"] == "null" ? '' : ($_GET["mdn"] == "_" ? '' : str_clean($_GET["mdn"]))) : '';
	$sms_status = isset($_GET["sms_status"]) ? ($_GET["sms_status"] == "_" ? '' : str_clean($_GET["sms_status"])) : '';
	$type = isset($_GET["type"]) ? ($_GET["type"] == "_" ? '' : str_clean($_GET["type"])) : '';
//	$seekerkodya = isset($_GET["seekerkodya"]) ? ($_GET["seekerkodya"] == "_" ? '' : str_clean($_GET["seekerkodya"])) : '';
	$seekerkodya = isset($_GET["seekerkodya"]) ? ($_GET["seekerkodya"] == "_" ? '' : ($_GET["seekerkodya"] == "0" ? '' :str_clean($_GET["seekerkodya"]))) : '';
//	$jobkodya = isset($_GET["jobkodya"]) ? ($_GET["jobkodya"] == "_" ? '' : str_clean($_GET["jobkodya"])) : '';
	$jobkodya = isset($_GET["jobkodya"]) ? ($_GET["jobkodya"] == "_" ? '' : ($_GET["jobkodya"] == "0" ? '' :str_clean($_GET["jobkodya"]))) : '';	
	
	$rel_id = isset($_GET["rel_id"]) ? ($_GET["rel_id"] == "_" ? "" : str_clean($_GET["rel_id"])) : "";
	$no_njfu = isset($_GET["no_njfu"]) ? ($_GET["no_njfu"] == "_" ? "" : str_clean($_GET["no_njfu"])) : "";

	/*
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
		
	}*/
	
	$search = "";
	if (!empty($date_send1_from)) $search .= "AND $t_jobs_send.date_send BETWEEN '$date_send1_from' AND '$date_send1_to' ";
	if (!empty($date_send2_from)) $search .= "AND DATE($t_log_sms.date_send) BETWEEN '$date_send2_from' AND '$date_send2_to' ";
	if (!empty($jobcat_id)) $search .= "AND $t_rel_subscriber_cat.jobcat_id = $jobcat_id ";	
	if (!empty($jobcat_id2)) $search .= "AND $t_jobs.jobcat_id = $jobcat_id2 ";	
	if (!empty($status) && ($status != "")) $search .= " AND jobs_send.status = $status ";
	if (!empty($jobseeker_name)) $search .= "AND $t_subscribers.name LIKE \"%$jobseeker_name%\" ";
	if (!empty($job_title)) $search .= "AND $t_jobs.title LIKE \"%$job_title%\" ";
	if (!empty($comp_id)) $search .= "AND $t_jobs.comp_id = $comp_id ";
	if (!empty($mdn)) $search .= "AND $t_subscribers.mdn LIKE \"%$mdn%\" ";
	if (!empty($rel_id)) $search .= "AND $t_rel_subscriber_cat.rel_id ='$rel_id' ";
	if (!empty($no_njfu)) $search .= "AND $t_jobs.description <>'No job for you'";
	if (!empty($type)) ($type=='APP')? $search .= "AND $t_subscribers.mentor_id=0 " : $search .= "AND $t_subscribers.mentor_id<>0 " ;
	if (!empty($sms_status)) $search .= "AND $t_log_sms.status='$sms_status'";
	//echo $job_title."<-".$search."<hr>";
	//if ($search != "") $search = substr($search,4);

	$sql = "SELECT $t_jobs_send.jobsend_id, DATE($t_jobs_send.date_send) AS date_send1, DATE_FORMAT($t_jobs_send.date_send,'%h:%i:%s %p') AS sent_time1,
		$t_rel_subscriber_cat.rel_id, $t_rel_subscriber_cat.jobcat_key,$t_subscribers.mentor_id, $t_subscribers.subscriber_id,
		$t_subscribers.mdn, $t_subscribers.`name` as jobseeker_name, 
		$t_subscribers.loc_id, $t_location.loc_type, $t_location.`name` as loc_title, $t_location.loc_lat, $t_location.loc_lng,
		$t_jobs.job_id, $t_jobs.title, $t_jobs.jobcat_id, $t_jobs.loc_id as job_loc_id,
		$t_companies.`name` as company_name,
		$t_log_sms.`status`, DATE($t_log_sms.date_send) AS date_send2, DATE_FORMAT($t_log_sms.date_send,'%h:%i:%s %p') AS sent_time2
		FROM
		$t_jobs_send
		INNER JOIN $t_log_sms ON $t_jobs_send.jobsend_id=$t_log_sms.jobsend_id
		INNER JOIN $t_rel_subscriber_cat ON $t_jobs_send.rel_id=$t_rel_subscriber_cat.rel_id
		INNER JOIN $t_subscribers ON $t_jobs_send.subscriber_id=$t_subscribers.subscriber_id
		INNER JOIN $t_location ON $t_subscribers.loc_id=$t_location.loc_id
		INNER JOIN $t_jobs ON $t_jobs_send.job_id=$t_jobs.job_id
		INNER JOIN $t_companies ON $t_jobs.comp_id=$t_companies.comp_id 		
		WHERE $t_log_sms.title='sms' 
	";
	if ($search != "") $sql .= " $search"; 
	//if ($search != "") $sql .= " WHERE $search";
	
	//echo $sql."<hr>";  //t_log_sms klo 0 APP, selain nol SMS
	
	$q = mysql_query($sql) OR die(output(mysql_error()));
	$arr["totaldata"] = mysql_num_rows($q);
	$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;

	$sql = getPagingQuery($sql,0,$page,$order,$ascdesc);
	//$sql = getPagingQuery($sql,$ndata,$page,$t_companies.".comp_id",$ascdesc);
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

	//cari kotamadya seeker mulai
		$loc_id = $row["loc_id"];
		$tsql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$loc_id'";
		$tsql = mysql_query($tsql) OR die(output(mysql_error()));
		$trow = mysql_fetch_assoc($tsql);
		$kecamatan_id = $trow["parent_id"];

		$tsql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kecamatan_id'";
		$tsql = mysql_query($tsql) OR die(output(mysql_error()));
		$trow = mysql_fetch_assoc($tsql);
		$kotamadya_id = $trow["parent_id"];

		$tsql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kotamadya_id'";
		$tsql = mysql_query($tsql) OR die(output(mysql_error()));
		$trow = mysql_fetch_assoc($tsql);
		$seeker_kodya = $trow["name"];
		//$province_id = $trow["parent_id"];
		$val["seeker_kodya"] = $seeker_kodya;
		$val["seeker_kodya_id"] = $kotamadya_id;
	//cari kotamadya seeker selesai
		$job_loc_id = $row["job_loc_id"];
		$tsql = "SELECT loc_lat, loc_lng, name, parent_id FROM $t_location WHERE loc_id='$job_loc_id'";
		$tsql = mysql_query($tsql) OR die(output(mysql_error()));
		$trow = mysql_fetch_assoc($tsql);
		$kecamatan_id = $trow["parent_id"];
		$val["pos_lat"] = $trow["loc_lat"];
		$val["pos_lng"] = $trow["loc_lng"];

		$tsql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kecamatan_id'";
		$tsql = mysql_query($tsql) OR die(output(mysql_error()));
		$trow = mysql_fetch_assoc($tsql);
		$kotamadya_id = $trow["parent_id"];
		
		$tsql = "SELECT name, parent_id FROM $t_location WHERE loc_id='$kotamadya_id'";
		$tsql = mysql_query($tsql) OR die(output(mysql_error()));
		$trow = mysql_fetch_assoc($tsql);
		$job_kodya = $trow["name"];
		//$province_id = $trow["parent_id"];
		$val["job_kodya"] = $job_kodya;			
		$val["job_kodya_id"] = $kotamadya_id;			
	//cari kotamadya job selesai
		
		$dis = 6371 * (SQRT((($subscriber_lat - $val["pos_lat"])*($subscriber_lat - $val["pos_lat"]))+(($subscriber_lng - $val["pos_lng"])*($subscriber_lng - $val["pos_lng"])))/360);
		$val["dis"] = $dis;
		//$val["dis"] = "satu".$subscriber_lat."-"."dua".$subscriber_lng."-"."tiga".$val["pos_lat"]."-"."empat".$val["pos_lng"];		
		array_push($arr["results"], $val);
		//echo "<pre>"; print_r($val); echo "</pre><hr>";
	}
	//echo "<pre>"; print_r($arr["results"]); echo "</pre><hr>";
//mulai pindah
	$temp2 = array(); //cari kodya
	if ($seekerkodya != "" || $jobkodya !="")
	{
		foreach ($arr["results"] as $job)
		{
			// array_find($needle, $haystack, $search_keys = false, $keys_to_search="") 
			if($seekerkodya !="" && $jobkodya == "") {
				$a = array_find($seekerkodya, array_map('strtolower', $job), false, "seeker_kodya_id");
				//echo $a."1<hr>";
				if (($a == "seeker_kodya_id") || ($a != ""))
				{
					//echo $a."<hr>";
					array_push($temp2, $job);
				}
			} else if($jobkodya !="" && $seekerkodya == "") {
				$a = array_find($jobkodya, array_map('strtolower', $job), false, "job_kodya_id");
				//echo $a."2<hr>";
				if (($a == "job_kodya_id") || ($a != ""))
				{
					//echo $a."<hr>";
					array_push($temp2, $job);
				}
			} else {
				$a = array_find($seekerkodya, array_map('strtolower', $job), false, "seeker_kodya_id");
				if (($a == "seeker_kodya_id") || ($a != "")) {
					array_push($temp2, $job);
				} else {
					$a = array_find($jobkodya, array_map('strtolower', $job), false, "job_kodya_id");
					if (($a == "job_kodya_id") || ($a != ""))
					{
						array_push($temp2, $job);
					}
				}

			}
			
		}		
	}
	else
		foreach ($arr["results"] as $job)
		{
				array_push($temp2, $job);
			//echo $a."<hr>";
		}
//pindah selesai
	//echo "<pre>"; print_r($arr["results"]); echo "</pre><hr>";
	$temp3 = array();	
	switch ($order)
	{ 
		case "jobsend_id" : $order = "jobsend_id"; break;
		case "date_send1" : $order = "date_send1"; break;			
		case "send_time1" : $order = "send_time1"; break;						
		case "rel_id" : $order = "rel_id"; break;			
		case "jobcat_key" : $order = "jobcat_key"; break;			
		case "sub_type" : $order = "sub_type"; break;			
		case "subscriber_id" : $order = "subscriber_id"; break;			
		case "mdn" : $order = "mdn"; break;									
		case "jobseeker_name" : $order = "jobseeker_name"; break;			
		case "loc_title" : $order = "loc_title"; break;						
		case "job_id" : $order = "job_id"; break;						
		case "title" : $order = "title"; break;						
		case "jobkodya" : $order = "jobkodya"; break;						
		case "company_name" : $order = "company_name"; break;
		case "dis" : $order = "dis"; break;						
		case "status" : $order = "status"; break;
		case "date_send2" : $order = "date_send2"; break;			
		case "send_time2" : $order = "send_time2"; break;									
		default : $order = "jobsend_id";
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
		

	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($temp2),true)); echo "</pre>";
?>