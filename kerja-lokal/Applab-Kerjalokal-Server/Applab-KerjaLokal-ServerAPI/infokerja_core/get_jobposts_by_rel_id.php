<?php

	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$rel_id = isset($_GET["rel_id"]) ? str_clean($_GET["rel_id"],1) : 0;
	$status = isset($_GET["status"]) ? str_clean($_GET["status"],1) : "";
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"],1) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"],1) : 0;
	$order = isset($_GET["order"]) ? str_clean($_GET["order"]) : "date_send";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean($_GET["ascdesc"]) : "asc";
	$callback = isset($_GET['callback']);

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
		case "loc_id" : $order = "$t_companies.loc_id"; break;
		case "status" : $order = "$t_jobs.status"; break;
		case "date_add" : $order = "$t_jobs.date_add"; break;
	}
	

	$sql = "SELECT *, $t_jobs.job_id AS job_id, $t_jobs.status AS status, $t_jobs.date_add AS job_date_add,
		$t_location.name AS loc_name
		FROM $t_rel_subscriber_cat 
		INNER JOIN $t_jobs_send ON $t_rel_subscriber_cat.rel_id = $t_jobs_send.rel_id
		INNER JOIN $t_jobs ON $t_jobs_send.job_id = $t_jobs.job_id
		INNER JOIN $t_location ON $t_location.loc_id = $t_jobs.loc_id
		WHERE $t_rel_subscriber_cat.rel_id = $rel_id";
	if ($status != "") $sql .= " AND $t_jobs_send.status = $status";

	//echo $sql."<hr>";
	$q = mysql_query($sql) OR die(output(mysql_error()));
	$arr["totaldata"] = mysql_num_rows($q);
	$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;

	$sql = getPagingQuery($sql,$ndata,$page,$order,$ascdesc);
	//$arr['pagingLink'] = getPagingLink($sql, $arr['ndata'], $page);
	$sql = mysql_query($sql) OR die(output(mysql_error()));

	$arr['nrows'] = mysql_num_rows($sql);
	$arr['nfields'] = mysql_num_fields($sql);
	$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
	$arr['page'] = $page;

	while($row = mysql_fetch_assoc($sql))
	{
		for($j=0;$j<$arr['nfields'];$j++)
		{
			$val[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
		}
		array_push($arr["results"], $val);
	}
	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr),true)); echo "</pre>";

?>