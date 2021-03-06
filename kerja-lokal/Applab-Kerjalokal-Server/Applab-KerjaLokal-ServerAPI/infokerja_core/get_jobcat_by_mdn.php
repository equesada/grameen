<?php

	require "conf.php";
	require "func.php";
	
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"],1) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"],1) : 0;
	if($_GET["order"] == "date_add" OR $_GET["order"] == "date_expired" OR $_GET["order"] == "status") $d = ""; else $d = $t_jobs_category.".";
	$order = isset($_GET["order"]) ? $d.str_clean($_GET["order"]) : "date_add";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean($_GET["ascdesc"]) : "asc";
	
	$date_add = isset($_GET["date_add"]) ? ($_GET["date_add"] == "_" ? "" : str_clean($_GET["date_add"])) : "";
	
	$arr["totaldata"] =0;
	$arr['ndata'] = 0;
	$arr['pagingLink'] = "";
	$arr['nrows'] = 0;
	$arr['nfields'] = 0;
	$arr['npage'] = 0;
	$arr['page'] = 0;
	$arr['results'] = array();
	
	switch ($order)
	{ 
		case "date_add" : $order = "$t_rel_subscriber_cat.date_add"; break;
		case "date_expired" : $order = "$t_rel_subscriber_cat.date_expired"; break;
		case "jobcat_title" : $order = "$t_jobs_category.jobcat_title"; break;
		case "status" : $order = "$t_rel_subscriber_cat.status"; break;
	}
	
	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$mdn = isset($_GET["mdn"]) ? $_GET["mdn"] : 0;
	$category = isset($_GET["category"]) ? $_GET["category"] : '';
	$date = date('Y-m-d');
	if($category=='active'){
		/*
		$sql = "SELECT *, $t_rel_subscriber_cat.date_add AS date_add,
		$t_rel_subscriber_cat.status AS status ,$t_job_posters.username AS username,
		$t_rel_subscriber_cat.date_update AS date_update
		FROM $t_rel_subscriber_cat 
		INNER JOIN $t_jobs_category ON $t_rel_subscriber_cat.jobcat_id = $t_jobs_category.jobcat_id 
		INNER JOIN $t_subscribers ON $t_subscribers.subscriber_id = $t_rel_subscriber_cat.subscriber_id 
		LEFT JOIN $t_job_posters ON $t_rel_subscriber_cat.update_by = $t_job_posters.jobposter_id 
		WHERE $t_subscribers.mdn='$mdn'
		AND $t_rel_subscriber_cat.date_expired >= '$date'
		*/
		$sql = "
			SELECT *, $t_rel_subscriber_cat.date_add AS date_add,
				$t_rel_subscriber_cat.status AS status ,$t_job_posters.username AS username,
				$t_rel_subscriber_cat.date_update AS date_update
			FROM $t_rel_subscriber_cat 
			LEFT JOIN $t_jobs_category ON $t_rel_subscriber_cat.jobcat_id = $t_jobs_category.jobcat_id 
			LEFT JOIN $t_subscribers ON $t_subscribers.subscriber_id = $t_rel_subscriber_cat.subscriber_id 
			LEFT JOIN $t_job_posters ON $t_rel_subscriber_cat.update_by = $t_job_posters.jobposter_id 
			WHERE $t_subscribers.mdn='$mdn' AND $t_rel_subscriber_cat.date_expired >= '$date' AND $t_rel_subscriber_cat.status IN (1,2,3)
		";
		//WHERE $t_subscribers.mdn='$mdn' AND $t_rel_subscriber_cat.date_expired <= '$date' AND $t_rel_subscriber_cat.status IN (1,2,3)
		$sql = mysql_query($sql) OR die(output(mysql_error()));
		$arr["totaldata"] = mysql_num_rows($sql);
		$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;
		
		$sql = "
			SELECT *, $t_rel_subscriber_cat.date_add AS date_add,
				$t_rel_subscriber_cat.status AS status ,$t_job_posters.username AS username,
				$t_rel_subscriber_cat.date_update AS date_update
			FROM $t_rel_subscriber_cat 
			LEFT JOIN $t_jobs_category ON $t_rel_subscriber_cat.jobcat_id = $t_jobs_category.jobcat_id 
			LEFT JOIN $t_subscribers ON $t_subscribers.subscriber_id = $t_rel_subscriber_cat.subscriber_id 
			LEFT JOIN $t_job_posters ON $t_rel_subscriber_cat.update_by = $t_job_posters.jobposter_id 
			WHERE $t_subscribers.mdn='$mdn' AND $t_rel_subscriber_cat.date_expired >= '$date' AND $t_rel_subscriber_cat.status IN (1,2,3)
		";
		
		$sql = getPagingQuery($sql,$ndata,$page,$order,$ascdesc);
		//echo $sql."<hr>";
		$sql = mysql_query($sql) OR die(output(mysql_error()));

		$arr['nrows'] = mysql_num_rows($sql);
		$arr['nfields'] = mysql_num_fields($sql);
		$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
		$arr['page'] = $page;
		$arr['results'] = array();
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

		
    }else{
        
		$sql = "SELECT *, $t_rel_subscriber_cat.date_add AS date_add, $t_rel_subscriber_cat.status AS status ,$t_job_posters.username AS username, 
		$t_rel_subscriber_cat.date_update AS date_update
		FROM $t_rel_subscriber_cat 
		INNER JOIN $t_jobs_category ON $t_rel_subscriber_cat.jobcat_id = $t_jobs_category.jobcat_id 
		INNER JOIN $t_subscribers ON $t_subscribers.subscriber_id = $t_rel_subscriber_cat.subscriber_id 
		LEFT JOIN $t_job_posters ON $t_rel_subscriber_cat.update_by = $t_job_posters.jobposter_id 
		WHERE $t_subscribers.mdn='$mdn'
		AND $t_rel_subscriber_cat.date_expired < '$date'
		AND $t_rel_subscriber_cat.status != '1'
		
		";
		
		/*
		$sql = "
			SELECT *, $t_rel_subscriber_cat.date_add AS date_add, $t_rel_subscriber_cat.status AS status ,$t_job_posters.username AS username, 
				$t_rel_subscriber_cat.date_update AS date_update
			FROM $t_rel_subscriber_cat 
			LEFT JOIN $t_jobs_category ON $t_rel_subscriber_cat.jobcat_id = $t_jobs_category.jobcat_id 
			LEFT JOIN $t_subscribers ON $t_subscribers.subscriber_id = $t_rel_subscriber_cat.subscriber_id 
			LEFT JOIN $t_job_posters ON $t_rel_subscriber_cat.update_by = $t_job_posters.jobposter_id 
			WHERE $t_subscribers.mdn='$mdn'	AND $t_rel_subscriber_cat.date_expired < '$date'
			AND $t_rel_subscriber_cat.status IN (2,3,4,5) 
			OR ($t_rel_subscriber_cat.status=5 AND $t_rel_subscriber_cat.date_expired <= '$date')
			GROUP BY $t_rel_subscriber_cat.jobcat_key
		";
		*/
		$sql = mysql_query($sql) OR die(output(mysql_error()));
		$arr["totaldata"] = mysql_num_rows($sql);
		$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;
		/*
		$sql = "
			SELECT *, $t_rel_subscriber_cat.date_add AS date_add, $t_rel_subscriber_cat.status AS status ,$t_job_posters.username AS username, 
				$t_rel_subscriber_cat.date_update AS date_update
			FROM $t_rel_subscriber_cat 
			LEFT JOIN $t_jobs_category ON $t_rel_subscriber_cat.jobcat_id = $t_jobs_category.jobcat_id 
			LEFT JOIN $t_subscribers ON $t_subscribers.subscriber_id = $t_rel_subscriber_cat.subscriber_id 
			LEFT JOIN $t_job_posters ON $t_rel_subscriber_cat.update_by = $t_job_posters.jobposter_id 
			WHERE $t_subscribers.mdn='$mdn'	AND $t_rel_subscriber_cat.date_expired < '$date'
			AND $t_rel_subscriber_cat.status IN (2,3,4,5)
			OR ($t_rel_subscriber_cat.status=5 AND $t_rel_subscriber_cat.date_expired <= '$date')
			GROUP BY $t_rel_subscriber_cat.jobcat_key
		";
		*/
		$sql = "SELECT *, $t_rel_subscriber_cat.date_add AS date_add, $t_rel_subscriber_cat.status AS status ,$t_job_posters.username AS username, 
		$t_rel_subscriber_cat.date_update AS date_update
		FROM $t_rel_subscriber_cat 
		INNER JOIN $t_jobs_category ON $t_rel_subscriber_cat.jobcat_id = $t_jobs_category.jobcat_id 
		INNER JOIN $t_subscribers ON $t_subscribers.subscriber_id = $t_rel_subscriber_cat.subscriber_id 
		LEFT JOIN $t_job_posters ON $t_rel_subscriber_cat.update_by = $t_job_posters.jobposter_id 
		WHERE $t_subscribers.mdn='$mdn'
		AND $t_rel_subscriber_cat.date_expired < '$date'
		AND $t_rel_subscriber_cat.status != '1'
		
		";
		$sql = getPagingQuery($sql,$ndata,$page,$order,$ascdesc);
		$sql = mysql_query($sql) OR die(output(mysql_error()));

		$arr['nrows'] = mysql_num_rows($sql);
		$arr['nfields'] = mysql_num_fields($sql);
		$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
		$arr['page'] = $page;
		$arr['results'] = array();
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

	}            

?>