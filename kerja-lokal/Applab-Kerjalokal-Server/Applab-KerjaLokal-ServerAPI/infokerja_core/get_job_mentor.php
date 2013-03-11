<?php
	require "conf.php";
	require "func.php";

	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$ndata = isset($_GET["ndata"]) ? str_clean(strtoupper($_GET["ndata"])) : 0;
	$page = isset($_GET["page"]) ? str_clean(strtoupper($_GET["page"])) : 0;
	$order = isset($_GET["order"]) ? str_clean(($_GET["order"])) : "company_name";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean(strtoupper($_GET["ascdesc"])) : "asc";
	$callback = isset($_GET['callback']);

	$sql = "SELECT * FROM $t_mentors WHERE country_id='$country_id'";
    //die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$arr["totaldata"] = mysql_num_rows($sql);
	$arr['ndata'] = $ndata == "0" ? $arr["totaldata"] : $ndata;

	$sql = "SELECT *, $t_mentors.name AS jobmentor_name, $t_location.name AS loc_name";
	$sql .= " FROM ($t_mentors LEFT JOIN $t_status ON $t_mentors.status = $t_status.status_id)";
	$sql .= " LEFT JOIN $t_location ON $t_mentors.loc_id = $t_location.loc_id WHERE $t_mentors.country_id='$country_id'";
	
    die($sql);
    
	$sql = getPagingQuery($sql,$ndata,$page,$order,$ascdesc);
	$arr['pagingLink'] = getPagingLink($sql, $arr['ndata'], $page);
	$sql = mysql_query($sql) OR die(output(mysql_error()));

	$arr['nrows'] = mysql_num_rows($sql);
	$arr['nfields'] = mysql_num_fields($sql);
	$arr['npage'] = 1;
	if($arr['nrows'] > 0)
	{
		$arr['npage'] = ceil ($arr["totaldata"] / $ndata);
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
echo "<pre>"; print_r(json_decode(output($arr))); echo "</pre>";
?>