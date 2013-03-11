<?php
/*
This code is owned by Grameen Applab Indonesia

Copying and re-using the code is prohibited without permission from  Grameen Applab Indonesia

-April 2012-
-Ramot Lubis-

*/

error_reporting(E_ERROR | E_WARNING | E_PARSE);

require "conf.php";
require "func.php";
	
$data = array();
$del_key = array("tx_id");
$tx_id = isset($_GET['tx_id']) ? str_clean($_GET['tx_id']) : 1;
$jobcat_id = isset($_GET['jobcat_id']) ? str_clean($_GET['jobcat_id'],1) : 1;
$subscriber_id = isset($_GET['subscriber_id']) ? str_clean($_GET['subscriber_id'],1) : 1;
$subscriber_id = $_GET["subscriber_id"];
$rel_id = isset($_GET['rel_id']) ? str_clean($_GET['rel_id'],1) : 1;
$job_n = isset($_GET['job_n']) ? str_clean($_GET['job_n'],1) : 1;
$date_add = isset($_GET['date_add']) ? str_clean($_GET['date_add']) : date("Y-m-d H:i:s");
$next_date = isset($date_add) ? date("Y-m-d H:i:s", strtotime($date_add) + 86400) : date("Y-m-d H:i:s", strtotime($date_add) + 86400);
$date_send = isset($_GET['date_send']) ? str_clean($_GET['date_send']) : date("Y-m-d H:i:s");
$temp = array();

// GET JOB MATCHING CRITERIA

$sql = "SELECT * FROM $t_job_match WHERE is_current='1'";
$sql = mysql_query($sql);
$r = mysql_fetch_assoc($sql);

$ratio_max_dis = $r["max_dis"] == "0" ? 1000000 : $r["max_dis"];
$ratio_max_nsend = $r["max_nsend"] == "0" ? 10000000 : $r["max_nsend"];
$ratio_dis = $r["dis"]/100;
$ratio_nsend = $r["nsend"]/100;
$ratio_expired = $r["expired"]/100;

//echo $sql."\n<br>";
// GET SUBSCRIBER'S LAT LONG

//$subscriber = CORE_URL."get_subscriber_by_subscriber_id.php?tx_id=$tx_id&subscriber_id=$subscriber_id";
$subscriber = "SELECT * FROM $t_subscribers WHERE subscriber_id='$subscriber_id'";
$subscriber = mysql_query($subscriber) OR die(output(mysql_error()));
$subscriber = mysql_fetch_assoc($subscriber);

//echo $sql."\n<br>";
//flush();

$subscriber_age = (strtotime(date("Y-m-d")) - strtotime($subscriber["birthday"])) / (365 * 86400);
if (($subscriber["pos_lat"] == "") || ($subscriber["pos_lat"] == "0"))
{
	$loc = "SELECT * FROM $t_location WHERE loc_id='".$subscriber["loc_id"]."'";
	$loc = mysql_query($loc) OR die(output(mysql_error()));
	$loc = mysql_fetch_assoc($loc);
	$subscriber_lat = $loc["loc_lat"];
	$subscriber_lng = $loc["loc_lng"];
}
else
{
	$subscriber_lat = $subscriber["pos_lat"];
	$subscriber_lng = $subscriber["pos_lng"];
}

$max_dis = 0;
$min_dis = 10000;
$max_expired = 1;
$min_expired = 365;
$max_nsend = 0;


// GET ALL QUALIFIED JOBS

$sql = "SELECT *, $t_jobs.job_id AS job_id FROM $t_jobs 
	LEFT JOIN $t_jobs_send ON $t_jobs_send.subscriber_id='$subscriber_id' AND $t_jobs.job_id = $t_jobs_send.job_id 
	WHERE $t_jobs_send.subscriber_id IS NULL AND
	jobcat_id='$jobcat_id' AND date_expired > '".date("Y-m-d")."' AND 
	(gender IS NULL OR gender='' OR gender='0' OR gender='1' OR gender='".$subscriber["gender"]."') AND 
	(edu_min IS NULL OR edu_min='' OR edu_min='0' OR edu_min <= '".$subscriber["edu_id"]."') AND 
	(age_min IS NULL OR age_min < $subscriber_age) AND
	(age_max IS NULL OR age_max > $subscriber_age) AND
	(salary_min >= '".$subscriber["salary"]."' OR salary_max >= '".$subscriber["salary"]."') AND
	$t_jobs.status='1'
	";
	
$sql = "SELECT *, $t_jobs.job_id AS job_id FROM $t_jobs 
	LEFT JOIN $t_jobs_send ON $t_jobs_send.subscriber_id='$subscriber_id' AND $t_jobs.job_id = $t_jobs_send.job_id 
	WHERE $t_jobs_send.subscriber_id IS NULL AND
	jobcat_id='$jobcat_id' AND date_expired > '".date("Y-m-d")."' AND
	$t_jobs.status='1'
	";

$sql = "SELECT * FROM $t_jobs 
	 
	WHERE date_expired > '".date("Y-m-d")."' AND
	$t_jobs.status='1' group by jobcat_id
	";

//echo $sql."\n<br>";
//flush();

	
if ($r["max_nsend"] > 0) $sql .= " AND n_send < '$ratio_max_nsend'";

//echo $sql."<br>";

$sql = mysql_query($sql);
$jobs = array();
$nrows = mysql_num_rows($sql);
$data["n_job"] = $nrows;

if ($nrows > 0)
{
	$nfields = mysql_num_fields($sql);


	// ADD DISTANCE TO THE JOB

	for ($jcat=1;$jcat<=14;$jcat++)
		$jobss[$jcat] = Array();
	while($r = mysql_fetch_assoc($sql))
	{
		for($j=0; $j<$nfields; $j++)
		{ $val[mysql_field_name($sql,$j)] = $r[mysql_field_name($sql,$j)]; }
		if (($r["pos_lat"] == "") || ($r["pos_lat"] == "0"))
		{
			$loc = "SELECT * FROM $t_location WHERE loc_id='".$r["loc_id"]."'";
			$loc = mysql_query($loc) OR die(output(mysql_error()));
			$loc = mysql_fetch_assoc($loc);
			$val["pos_lat"] = $loc["loc_lat"];
			$val["pos_lng"] = $loc["loc_lng"];
		}
		$dis = 6371 * (SQRT((($subscriber_lat - $val["pos_lat"])*($subscriber_lat - $val["pos_lat"]))+(($subscriber_lng - $val["pos_lng"])*($subscriber_lng - $val["pos_lng"])))/360);
		$val["dis"] = $dis;
		
		// $expired = new DateTime($r["date_expired"]);
		// $curr_date = new DateTime(date("Y-m-d"));
		// $val["expired"] = $expired->diff($curr_date);
		
		$val["expired"] = (strtotime($r["date_expired"]) - strtotime(date("Y-m-d"))) / 86400;
		
		$jcat = $r['jobcat_id'];
		array_push($jobss[$jcat], $val);
		
		//array_push($jobs, $val);

	}
	
	//print_r($jobs);


	
	for ($jcat=1;$jcat<=14;$jcat++)
		$tempp[$jcat] = Array();

	// GET MAX AND MIN MEASUREMENT

	
	foreach($jobss as $jcat=>$jobs) {
	$a = 0;
	foreach($jobs as $job)
	{ 
		if (($job["dis"] < $ratio_max_dis) && ($job["expired"] > 0))
		{
			$tempp[$jcat][$a] = $job; 
			$max_dis = $job["dis"] > $max_dis ?  $job["dis"] : $max_dis;
			$min_dis = $job["dis"] < $min_dis ?  $job["dis"] : $min_dis;
			$max_expired = $job["expired"] > $max_expired ?  $job["expired"] : $max_expired;
			$min_expired = $job["expired"] < $min_expired ?  $job["expired"] : $min_expired;
			$max_nsend = $job["n_send"] > $max_nsend ? $job["n_send"] : $max_nsend;
			$a++;
		}
	}
	unset($jobs);
	//echo "<pre>"; print_r($temp); echo "</pre><hr>";
	}
	
	
	
	foreach($tempp as $jcat=>$temp) {
	$data[$jcat] = Array();
	if (sizeof($temp) > 0)
	{
		$jobs = $temp;
		$data[$jcat]["n_qualified_job"] = sizeof($jobs);
		
	
		
		$a = 0;


		// GET JOB'S WEIGHT

		foreach($jobs as $job)
		{
			if ($max_dis != $min_dis)
				$jobs[$a]["w_dis"] = (($job["dis"]-$min_dis)/($max_dis-$min_dis))*100;
			else
				$jobs[$a]["w_dis"] = 100;
			
			if ($max_expired != $min_expired)
				$jobs[$a]["w_expired"] = (($job["expired"]-$min_expired)/($max_expired-$min_expired))*100;
			else
				$jobs[$a]["w_expired"] = 100;
			
			if (($job["n_send"] == "0") || ($max_nsend == "0"))
				$jobs[$a]["w_nsend"] = 0;
			else if ($job["n_send"] == $max_nsend)
				$jobs[$a]["w_nsend"] = 100;
			else
				$jobs[$a]["w_nsend"] = 100-((($max_nsend-$job["n_send"])/$max_nsend)*100);
				
			$jobs[$a]["weight"] = ($ratio_dis * $jobs[$a]["w_dis"]) + ($ratio_nsend * $jobs[$a]["w_nsend"]) + ($ratio_expired * $jobs[$a]["w_expired"]);
			$a++;
		}
		//echo sizeof($jobs)."<br>";
		//echo "<pre>"; print_r($jobs); echo "</pre><hr>";


		// SORTING PROCCESS

		$tmp = Array();
		foreach($jobs as $job) 
			$tmp[] = $job["weight"];
		array_multisort($tmp, $jobs);
		$a = 0;
		
		//echo "<pre>"; print_r($jobs); echo "</pre>";


		// ADD THE MOST RELEVANT JOB TO TABLE jobs_send

		//$sql = "INSERT INTO $t_jobs_send (job_id, subscriber_id, date_add, date_send, status, rel_id) VALUES ('".$jobs[0]["job_id"]."', '$subscriber_id', '$date_add', '".date("Y-m-d", time()+(($a+1)*86400))."','1','$rel_id')";

		
		$cc = 0;
		
		$tenJobs = Array();
		while ($cc < count($jobs)) {
		
			$tenJobs[$cc] = $jobs[$cc];
			$njobs = $cc+1;
			if ($cc ==9)
				$cc = count($jobs);
		
			$cc++;
		}
		
		$data[$jcat]["n_jobs"] = $njobs;
		$data[$jcat]["jobsend_id"] = $r["jobsend_id"];
		//$data["results"] = $tenJobs;



		// ADD UP TO 7 UNIQUE JOBS TO TABLE jobs_send

		//echo "<table border=1>";
		//echo "<tr><td>ID</td><td>dis</td><td>nsend</td><td>expired</td><td>w_dis</td><td>w_nsend</td><td>w_expired</td><td>Total weight</td><td>Date Send</td></tr>";


		$data[$jcat]["status"] = "1";
		//echo output($data);
	}
	else
	{
		$data["n_qualified_job"] = 0;
		// ADD NO JOB FOR YOU TO TABLE jobs_send

		//$sql = "INSERT INTO $t_jobs_send (job_id, subscriber_id, date_add, date_send, status, rel_id) VALUES ('".$jobs[0]["job_id"]."', '$subscriber_id', '$date_add', '".date("Y-m-d", time()+(($a+1)*86400))."','1','$rel_id')";


		//$data["jobsend_id"] = $r["jobsend_id"];
		//$data["job_id"] = "1";
		$data[$jcat]["status"] = "1";
		$data[$jcat]["n_jobs"] = 0;
		

			
		//echo output($data);
	}
  }
  echo output($data);
}
else
{
	$data["n_qualified_job"] = 0;
	// ADD NO JOB FOR YOU TO TABLE jobs_send

	//$sql = "INSERT INTO $t_jobs_send (job_id, subscriber_id, date_add, date_send, status, rel_id) VALUES ('".$jobs[0]["job_id"]."', '$subscriber_id', '$date_add', '".date("Y-m-d", time()+(($a+1)*86400))."','1','$rel_id')";

	$data["jobsend_id"] = $r["jobsend_id"];
	$data["job_id"] = "1";
	$data["status"] = "1";
	$data["n_jobs"] = 0;


	echo output($data);
}

?>