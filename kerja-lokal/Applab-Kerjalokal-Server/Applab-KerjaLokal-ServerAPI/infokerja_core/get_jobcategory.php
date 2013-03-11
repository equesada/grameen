<?php

	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"]) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"]) : 1;
	if($_GET["order"] == "date_add" OR $_GET["order"] == "date_update") $d =  $t_education."."; else $d = "";
	$order = isset($_GET["order"]) ? $d.str_clean(strtoupper($_GET["order"])) : "jobcat_title";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean(strtoupper($_GET["ascdesc"])) : "ASC";
	$callback = isset($_GET['callback']);

	$sql = "SELECT * FROM $t_jobs_category WHERE country_id='$country_id'";
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$arr["totaldata"] = mysql_num_rows($sql);
	//$arr['ndata'] = $ndata == "0" ? $arr["totaldata"] : $ndata;
	
	//$sql = "SELECT *, $t_status.title AS status_title FROM $t_status INNER JOIN $t_jobs_category ON $t_jobs_category.status = $t_status.status_id";
	/*
	$sql = getPagingQuery($sql,$ndata,$page,$order,$ascdesc);
	$arr['pagingLink'] = getPagingLink($sql, $arr['ndata'], $page);
	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
    */
	$arr['nrows'] = mysql_num_rows($sql);
	$arr['nfields'] = mysql_num_fields($sql);
	//$arr['npage'] = ceil ($arr["totaldata"] / $arr['ndata']);
	//$arr['page'] = $page;
	$arr['results'] = array();
	
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