<?php

	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$id = isset($_GET["id"]) ? str_clean(($_GET["id"]),1) : 0;
    $status = isset($_GET["status"]) ? str_clean($_GET["status"],1) : "";
    
    
	//$sql = "SELECT *, $t_rel_subscriber_cat.date_add AS date_add, $t_rel_subscriber_cat.status AS status 
	//FROM $t_rel_subscriber_cat INNER JOIN $t_jobs_category ON $t_rel_subscriber_cat.jobcat_id = $t_jobs_category.jobcat_id WHERE subscriber_id='$id'
	//and $t_rel_subscriber_cat.status ='$status'";
	
	
	if ($status != "")
		$sql = "SELECT DISTINCT *, rel_subscriber_cat.date_add AS DATE_ADD, rel_subscriber_cat.status AS STATUS 
		FROM rel_subscriber_cat 
		INNER JOIN jobs_category ON rel_subscriber_cat.jobcat_id = jobs_category.jobcat_id 
		WHERE rel_subscriber_cat.subscriber_id='$id' AND rel_subscriber_cat.status ='$status'
		GROUP BY jobs_category.jobcat_title";
    else
		$sql = "SELECT DISTINCT *, rel_subscriber_cat.date_add AS DATE_ADD, rel_subscriber_cat.status AS STATUS 
		FROM rel_subscriber_cat 
		INNER JOIN jobs_category ON rel_subscriber_cat.jobcat_id = jobs_category.jobcat_id 
		WHERE rel_subscriber_cat.subscriber_id='$id'
		GROUP BY jobs_category.jobcat_title";
                
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
	//echo "<pre>"; print_r(json_decode(output($arr),true)); echo "</pre>";

?>