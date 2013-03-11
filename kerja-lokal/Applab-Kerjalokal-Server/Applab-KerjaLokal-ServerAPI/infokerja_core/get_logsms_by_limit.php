<?php

	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$rel_id = isset($_GET["rel_id"]) ? str_clean(($_GET["rel_id"]),1) : 0;
	$id = isset($_GET["id"]) ? str_clean(($_GET["id"]),1) : 0;
	$jobcat_id = isset($_GET["jobcat_id"]) ? str_clean(($_GET["jobcat_id"]),1) : 0;
    $status = isset($_GET["status"]) ? str_clean($_GET["status"],1) : "";
    $order = isset($_GET["order"]) ? str_clean($_GET["order"]) : "date_send";
    $ascdesc = isset($_GET["ascdesc"]) ? str_clean($_GET["ascdesc"]) : "desc";
	/*
	$sql = "SELECT log_sms.subscriber_id, log_sms.log_id, log_sms.title, log_sms.msg, log_sms.status, rel_subscriber_cat.subscriber_id, rel_subscriber_cat.jobcat_id   
	FROM log_sms 
	INNER JOIN rel_subscriber_cat 
	ON log_sms.subscriber_id =  rel_subscriber_cat.subscriber_id 
	WHERE log_sms.subscriber_id='$id' AND rel_subscriber_cat.`jobcat_id`='$jobcat_id' AND log_sms.status='$status'";
	*/
	$sql = "SELECT log_sms.subscriber_id, log_sms.log_id, log_sms.title, log_sms.msg, log_sms.status, log_sms.job_id, jobs.jobcat_id
	FROM log_sms 
	INNER JOIN jobs
	ON log_sms.job_id = jobs.job_id
	WHERE log_sms.subscriber_id='$id' AND log_sms.jobcat_id='$jobcat_id' AND log_sms.status='$status'
	ORDER BY date_send DESC";
	 
	//die($sql); 
	
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$arr["nfields"] = mysql_num_fields($sql);
	$arr["totaldata"] = mysql_num_rows($sql); 
	$arr["results"] = array();
	$temp = array();
	while ($row = mysql_fetch_assoc($sql))
	{
		for($j=0;$j<$arr['nfields'];$j++)
		{
			$temp[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
		}
		array_push($arr["results"], $temp);
	}
	
	echo output($arr);
?>