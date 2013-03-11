<?php

	require "conf.php";
	require "func.php";
	
	//$arr = $_GET["date_active"];
	//print_r($arr);
	//$date_active_from = $arr['from_date'];
	//$date_active_to = $arr['to_date'];
	
	list($date_active_from, $date_active_to) = explode(":", $_GET["date_add"]);	
	
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"],1) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"],1) : 0;
	$order = isset($_GET["order"]) ? str_clean($_GET["order"]) : "rel_id";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean($_GET["ascdesc"]) : "asc";
	$callback = isset($_GET['callback']);
	
	$status = isset($_GET["status"]) ? ($_GET["status"] == "0" ? "0" : str_clean($_GET["status"])) : "";
	$subscriber_name = isset($_GET["subscriber_name"]) ? ($_GET["subscriber_name"] == "_" ? "" : str_clean($_GET["subscriber_name"])) : "";	
	$mentor_name = isset($_GET["mentor_name"]) ? ($_GET["mentor_name"] == "_" ? '' : str_clean($_GET["mentor_name"])) : '';	
	$mdn = isset($_GET["mdn"]) ? ($_GET["mdn"] == "_" ? '' : str_clean($_GET["mdn"])) : '';
	$date_expired = isset($_GET["date_expired"]) ? ($_GET["date_expired"] == "_" ? "" : str_clean($_GET["date_expired"])) : "";
	$date_active_from = isset($date_active_from) ? ($date_active_from == '_' ? '' : str_clean($date_active_from)) : '';
	//echo $date_active_from."<hr>";
	$date_active_to = isset($date_active_to) ? ($date_active_to == "_" ? '' : str_clean($date_active_to)) : '';	
	//echo $date_active_to."<hr>";
	
	//$jobcat_id = isset($_GET["jobcat_id"]) ? ($_GET["jobcat_id"] == "_" ? '' : str_clean($_GET["jobcat_id"])) : '';
	$jobcat_id = isset($_GET["jobcat_id"]) ? ($_GET["jobcat_id"] == "_" ? '' : ($_GET["jobcat_id"] == "_" ? '' : str_clean($_GET["jobcat_id"]))) : '';
	//$loc_title = isset($_GET["loc_title"]) ? ($_GET["loc_title"] == "_" ? '' : str_clean($_GET["loc_title"])) : '';
	$loc_title = isset($_GET["loc_title"]) ? ($_GET["loc_title"] == "null" ? '' : str_clean($_GET["loc_title"])) : '';	
	//echo $loc_title."<hr>";
	
	$arr["totaldata"] =0;
	$arr['ndata'] = 0;
	$arr['pagingLink'] = "";
	$arr['nrows'] = 0;
	$arr['nfields'] = 0;
	$arr['npage'] = 0;
	$arr['page'] = 0;
	$arr['results'] = array();
	
	
	$search = "";
	if ($subscriber_name != "") $search .= "AND $t_subscribers.name LIKE \"%$subscriber_name%\"";
	if ($gender != "") $search .= "AND $t_subscribers.gender = \"$gender\"";
	if ($education != "") $search .= "AND $t_subscribers.edu_id = \"$education\"";
	if (!empty($jobseeker_name)) $search .= "AND $t_subscribers.name LIKE \"%$jobseeker_name%\" ";
	if (!empty($mdn)) $search .= "AND $t_subscribers.mdn LIKE \"%$mdn%\" ";
	if (!empty($mentor_name)) $search .= "AND $t_subscribers.name LIKE \"%$mentor_name%\" ";	
	if (!empty($birthday_date_from)) $search .= "AND $t_subscribers.birthday BETWEEN '$birthday_date_from' AND '$birthday_date_to' ";
	if (!empty($submit_date_from)) $search .= "AND $t_subscribers.date_add BETWEEN '$submit_date_from 00:00:01'  AND '$submit_date_to 00:00:01' ";
	if (!empty($edit_date_from)) $search .= "AND $t_subscribers.date_update BETWEEN '$edit_date_from 00:00:01'  AND '$edit_date_to 00:00:01' ";	
	if (!empty($date_active_from)) $search .= "AND $t_rel_subscriber_cat.date_add BETWEEN '$date_active_from 00:00:01' AND '$date_active_to 23:59:59' ";	
	if (!empty($status) && ($status != "")) $search .= " AND $t_rel_subscriber_cat.status = $status ";
	
	if (!empty($jobcat_id)) $search .= "AND $t_rel_subscriber_cat.jobcat_id = $jobcat_id ";
	//if ($date_add != "") $search .= "AND $t_jobs.date_add LIKE \"%$date_add%\"";
	
	//echo $search."<hr>";	
	$sql .= "SELECT $t_rel_subscriber_cat.rel_id AS rel_id, $t_rel_subscriber_cat.status AS status, 
		$t_rel_subscriber_cat.date_add AS date_add, $t_rel_subscriber_cat.date_active AS date_active, $t_rel_subscriber_cat.date_expired AS date_expired, $t_jobs_category.jobcat_title,
		$t_subscribers.mdn AS mdn, $t_subscribers.subscriber_id AS subscriber_id, $t_subscribers.name AS subscriber_name, $t_subscribers.loc_id AS loc_id,
		$t_subscribers.mentor_id, $t_location.name AS loc_title
		FROM $t_subscribers 
		INNER JOIN $t_rel_subscriber_cat ON $t_subscribers.subscriber_id = $t_rel_subscriber_cat.subscriber_id
		INNER JOIN $t_location ON $t_subscribers.loc_id = $t_location.loc_id
		/*INNER JOIN $t_mentors ON $t_subscribers.subscriber_id = $t_mentors.subscriber_id*/
		INNER JOIN $t_jobs_category ON $t_rel_subscriber_cat.jobcat_id = $t_jobs_category.jobcat_id
		";	
	
	if ($search != "") $sql .= $search;	
		
	//echo $sql."<hr>";
	
	//$sql = mysql_query($sql) OR die(output(mysql_error()));
	
	//$arr['results'] = array();
	//$val = array();
	
	
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	
	//$arr['nrows'] = mysql_num_rows($sql);
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
			
		//$subscriber_id = $val["subscriber_id"];		
		$rel_id = $val["rel_id"];						
		/*
		$sql2 =	"SELECT *,
					SUM(IF(job_id = '1', 1,0)) AS `n_njfu`, 
					SUM(IF(job_id != '1', 1,0)) AS `n_job`, 
					COUNT(job_id) AS `n_all_job` 
					FROM `jobs_send`					
					WHERE `rel_id`='$rel_id' GROUP BY `jobs_send`.`rel_id`				
				";					
		// and status='2'		
		*/
		$sql2 = "SELECT *, 
					SUM(IF(`log_sms`.`job_id` = '1', 1,0)) AS `n_njfu`, 
					SUM(IF(`log_sms`.`job_id` != '1', 1,0)) AS `n_job`, 
					COUNT(`log_sms`.`job_id`) AS `n_all_job` 
					FROM `jobs_send` 
					inner join `log_sms` on `jobs_send`.`jobsend_id` = `log_sms`.`jobsend_id`
					WHERE `log_sms`.`rel_id`='$rel_id' and `log_sms`.`status`='2' GROUP BY `log_sms`.`rel_id`  		
				";
		//echo $sql2."<hr>";		
		$sql2 = mysql_query($sql2) OR die(output(mysql_error()));
		$row2 = mysql_fetch_assoc($sql2);
		$val["n_njfu"] = ($row2["n_njfu"])?$row2["n_njfu"]:0;		
		$val["n_job"] = ($row2["n_job"])?$row2["n_job"]:0;
		$val["n_all_job"] = ($row2["n_all_job"])?$row2["n_all_job"]:0;
		
		if ($val["mentor_id"] != 0)
		{
			$sql2 = "SELECT subscriber_id FROM $t_mentors WHERE mentor_id='".$val["mentor_id"]."'";
			$sql2 = mysql_query($sql2) OR die(output(mysql_error()));
			$row2 = mysql_fetch_assoc($sql2);
			$subscriber_id = $row2["subscriber_id"];
			$sql2 = "SELECT name FROM $t_subscribers WHERE subscriber_id='$subscriber_id'";
			$sql2 = mysql_query($sql2) OR die(output(mysql_error()));
			$row2 = mysql_fetch_assoc($sql2);
			$val["mentor_name"] = $row2["name"];
		} else $val["mentor_name"] = "SELF";
		

		array_push($arr["results"], $val);
		//echo "<pre>"; print_r($val); echo "</pre><hr>";	
	}
		
	$b = 0;
	//echo "<pre>"; print_r($arr["results"]); echo "</pre><hr>";		
	
	$temp2 = array();
	if ($loc_title != "" || $loc_title != 0)
	{
		foreach ($arr["results"] as $job)
		{
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
	{
		//$temp2 = array();
		foreach ($arr["results"] as $job)
		{
				array_push($temp2, $job);
			
		}
	}
	//echo "<pre>"; print_r($temp2); echo "</pre><hr>";
	
		$temp3 = array();
		switch ($order)
		{ 
			case "rel_id" : $order = "rel_id"; break;
			case "status_title" : $order = "status_title"; break;
			case "jobcat_title" : $order = "jobcat_title"; break;
			case "jobcat_id" : $order = "jobcat_id"; break;	
			case "date_active" : $order = "date_active"; break;
			case "date_expired" : $order = "date_expired"; break;
			case "n_job" : $order = "n_job"; break;
			case "n_njfu" : $order = "n_njfu"; break;
			case "n_all_job" : $order = "n_all_job"; break;
			case "mdn" : $order = "mdn"; break;
			case "subscriber_name" : $order = "subscriber_name"; break;
			case "province_name" : $order = "province_name"; break;
			case "kotamadya_name" : $order = "kotamadya_name"; break;
			case "kecamatan_name" : $order = "kecamatan_name"; break;
			case "kelurahan_name" : $order = "kelurahan_name"; break;
			case "mentor_name" : $order = "mentor_name"; break;
			
			
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
		//echo "<pre>"; print_r($temp3); echo "</pre>";
		unset($arr["results"]);
		$arr["results"] = array();
		
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
			//$temp2[$a]["no"] = $a+1;
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
	
		

	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr),true)); echo "</pre>";

?>