<?php

	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$id = isset($_GET["id"]) ? str_clean(($_GET["id"]),1) : 0;
    $status = isset($_GET["status"]) ? str_clean($_GET["status"],1) : "";
    //echo $status."<hr>";
	/*
	$sql = "SELECT *, $t_rel_subscriber_cat.date_add AS date_add, $t_rel_subscriber_cat.status AS status 
		FROM $t_rel_subscriber_cat INNER JOIN $t_jobs_category ON $t_rel_subscriber_cat.jobcat_id = $t_jobs_category.jobcat_id WHERE subscriber_id='$id' order by $t_rel_subscriber_cat.rel_id desc";
	*/
	$sql = "SELECT *, $t_rel_subscriber_cat.date_add AS date_add, $t_rel_subscriber_cat.status AS status 
		FROM $t_rel_subscriber_cat INNER JOIN $t_jobs_category ON $t_rel_subscriber_cat.jobcat_id = $t_jobs_category.jobcat_id WHERE subscriber_id='$id'
        and $t_rel_subscriber_cat.status=1";	
	//die($sql);	

	$sql = mysql_query($sql) OR die(output(mysql_error()));	
	$arr["nfields"] = mysql_num_fields($sql);
	$arr["totaldata"] = mysql_num_rows($sql); 
	$arr["results"] = array();
	$temp = array();
	$jobcat_key = null;
	
	
	while ($row = mysql_fetch_assoc($sql))
	{
		for($j=0;$j<$arr['nfields'];$j++)
		{
			$temp[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];				
		}		

		array_push($arr["results"], $temp);						
		//print_r($new);
		//$jobcat_key .= $temp["jobcat_key"].'_'.$temp["date_expired"]."_";
	}	

	
	
	//$jobcat_key = $jobcat_key;
	//echo $jobcat_key;
	//$jobcat = explode("_", $jobcat_key);				
	//$val = array_unique($arr);
	
	echo output($arr);
	//echo "<br><br><br>";
	//echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr),true)); echo "</pre>";
?>