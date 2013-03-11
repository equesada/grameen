<?php

	require "conf.php";
	require "func.php";
	
	$mdn = isset($_GET["mdn"]) ? str_clean($_GET["mdn"]) : 0;


	$sql = "SELECT *, $t_rel_subscriber_cat.date_expired AS date_expired, $t_rel_subscriber_cat.status AS status  
		FROM ($t_rel_subscriber_cat 
		INNER JOIN $t_jobs_category ON $t_rel_subscriber_cat.jobcat_id = $t_jobs_category.jobcat_id) 
		INNER JOIN $t_subscribers ON $t_rel_subscriber_cat.subscriber_id = $t_subscribers.subscriber_id 
		WHERE mdn='$mdn'";
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	
	$arr = array();
	$temp = "";
	while ($row = mysql_fetch_assoc($sql))
	{
		for($j=0;$j<mysql_num_fields($sql);$j++)
		{
			$arr[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
		}
	}
	
	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr),true)); echo "</pre>";

?>