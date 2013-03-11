<?php

	require "conf.php";
	require "func.php";
	
	$date_send = isset($_GET["date_send"]) ? str_clean($_GET["date_send"]) : "";

	$sql = "SELECT *, $t_jobs.job_id AS job_id, $t_subscribers.subscriber_id AS subscriber_id,
		$t_jobs.status AS status, $t_jobs.description AS description, $t_jobs_send.rel_id AS rel_id
		
		FROM ($t_jobs_send INNER JOIN $t_jobs ON $t_jobs_send.job_id = $t_jobs.job_id)
		INNER JOIN $t_job_posters ON $t_jobs.jobposter_id = $t_job_posters.jobposter_id
		INNER JOIN $t_companies ON $t_companies.comp_id = $t_job_posters.comp_id
		INNER JOIN $t_subscribers ON $t_jobs_send.subscriber_id = $t_subscribers.subscriber_id 
		INNER JOIN $t_rel_subscriber_cat ON $t_jobs_send.rel_id = $t_rel_subscriber_cat.rel_id
		WHERE date_send='$date_send'";
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