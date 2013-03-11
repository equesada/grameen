<?php

	require "conf.php";
	require "func.php";
	
	$key = isset($_GET["key"]) ? str_clean($_GET["key"]) : "";
	$value = isset($_GET["value"]) ? str_clean($_GET["value"]) : "";

	$sql = "SELECT *, $t_jobs.job_id AS job_id, $t_subscribers.subscriber_id AS subscriber_id,
		$t_jobs.status AS status, $t_jobs.description AS description, $t_jobs_send.rel_id AS rel_id,
		$t_jobs_send.status AS jobsend_status, $t_rel_subscriber_cat.n_jobreceived AS n_jobreceived
		FROM ($t_jobs_send INNER JOIN $t_jobs ON $t_jobs_send.job_id = $t_jobs.job_id)
		INNER JOIN $t_job_posters ON $t_jobs.jobposter_id = $t_job_posters.jobposter_id
		INNER JOIN $t_companies ON $t_companies.comp_id = $t_job_posters.comp_id
		INNER JOIN $t_subscribers ON $t_jobs_send.subscriber_id = $t_subscribers.subscriber_id 
		INNER JOIN $t_rel_subscriber_cat ON $t_jobs_send.rel_id = $t_rel_subscriber_cat.rel_id";
		
	if ($key != "") 
	{
		$sql .= " WHERE ";
		$key = explode("|",$key);
		$value = explode("|",$value);
		$a = 0;
		foreach ($key as $key2)
		{ 
			switch ($key2)
			{
				case "rel_id" 			: $key2 = $t_rel_subscriber_cat.".rel_id"; break;
				case "job_id" 			: $key2 = $t_jobs.".job_id"; break;
				case "subscriber_id" 	: $key2 = $t_subscribers.".subscriber_id"; break;
				case "status" 			: $key2 = $t_jobs.".status"; break;
				case "jobsend_status"	: $key2 = $t_jobs_send.".status"; break;
				case "description" 		: $key2 = $t_jobs.".description"; break;
				case "jobposter_id" 	: $key2 = $t_jobs.".jobposter_id"; break;
				case "comp_id" 			: $key2 = $t_companies.".comp_id"; break;
			}
			$sql .= $key2." = \"$value[$a]\""; 
			$a++;
			if ($a < sizeof($key)) $sql .= " AND ";
		}
	}
	
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));

	$arr['nrows'] = mysql_num_rows($sql);
	$arr['nfields'] = mysql_num_fields($sql);
	$arr["results"] = array();
	$i = 0;
	while($row = mysql_fetch_assoc($sql))
	{
		for($j=0;$j<$arr['nfields'];$j++)
		{
			$val[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
		}
		array_push($arr["results"], $val);
		$i++;
	}
	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr),1)); echo "</pre>";

?>