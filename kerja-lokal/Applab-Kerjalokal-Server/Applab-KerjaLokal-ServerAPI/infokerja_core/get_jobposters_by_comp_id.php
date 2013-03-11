<?php
	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"],1) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"],1) : 0;
	$order = isset($_GET["order"]) ? str_clean(($_GET["order"])) : "company_name";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean(strtoupper($_GET["ascdesc"])) : "ASC";
	$callback = isset($_GET['callback']);

	$comp_id = isset($_GET["comp_id"]) ? str_clean($_GET["comp_id"],1) : 0;
	
	$sql = "SELECT * FROM $t_job_posters WHERE country_id='$country_id' AND comp_id='$comp_id'";
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$arr["totaldata"] = mysql_num_rows($sql);
	$arr['ndata'] = $ndata == "0" ? $arr["totaldata"] : $ndata;

	$sql = "SELECT *, $t_job_posters.username AS jobposter_name, $t_job_posters.status AS jobposter_status, $t_companies.name AS company_name";
	$sql .= " FROM ($t_job_posters LEFT JOIN $t_status ON $t_job_posters.status = $t_status.status_id)";
	$sql .= " INNER JOIN $t_companies ON $t_companies.comp_id = $t_job_posters.comp_id";
	$sql .= " WHERE $t_job_posters.country_id='$country_id' AND $t_job_posters.comp_id='$comp_id'";
	
	$sql = getPagingQuery($sql,$ndata,$page,$order,$ascdesc);
	$arr['pagingLink'] = getPagingLink($sql, $arr['ndata'], $page);
	$sql = mysql_query($sql) OR die(output(mysql_error()));

	$arr['nrows'] = mysql_num_rows($sql);
	$arr['nfields'] = mysql_num_fields($sql);
	$arr['npage'] = 1;
	if($arr['nrows'] > 0)
	{
		$arr['npage'] = ceil ($arr["totaldata"] / $arr['nrows']);
	}
	$arr['page'] = $page;
	$arr['results'] = array();

	while($row = mysql_fetch_assoc($sql))
	{
		for($j=0;$j<$arr['nfields'];$j++)
		{
			$val[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
		}
		array_push($arr["results"], $val);
	}

	echo output($arr);
//echo "<pre>"; print_r(json_decode(output($arr))); echo "</pre>";
?>