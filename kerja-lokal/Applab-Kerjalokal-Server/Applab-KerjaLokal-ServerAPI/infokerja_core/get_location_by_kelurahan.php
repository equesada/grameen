<?php
	require_once "conf.php";
	require_once "func.php";
	
	$country_id = isset($_GET["country_id"]) ? str_clean(strtoupper($_GET["country_id"])) : "ID";
	$ndata = isset($_GET["ndata"]) ? str_clean(strtoupper($_GET["ndata"])) : 0;
	$page = isset($_GET["page"]) ? str_clean(strtoupper($_GET["page"])) : 0;
	$order = isset($_GET["order"]) ? str_clean(strtoupper($_GET["order"])) : "name";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean(strtoupper($_GET["ascdesc"])) : "asc";
	$callback = isset($_GET['callback']);
	$arr = array(
					"status" => "1",
					"msg" => "Success Select"
					);
					
	$sql = "SELECT * FROM $t_location WHERE loc_type='KELURAHAN'";
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$arr["totaldata"] = mysql_num_rows($sql);
	$arr['ndata'] = $ndata == "0" ? $arr["totaldata"] : $ndata;

	/*$field = isset($_GET['field']) ? $_GET['field'] : "0";
	$value = isset($_GET['value']) ? $_GET['value'] : "";
	
	if($field != "0" || $value != "")
	{
		if(is_string($field))
		{
			$sql .= " AND ".$field." = '".$value."'";
		}
		else
		{
			$sql .= " AND ".$field." = ".$value;
		}
	}
	*/
	
	$sql = "SELECT * FROM $t_location WHERE loc_type='KELURAHAN'";
	$sql = getPagingQuery($sql,$ndata,$page,$order,$ascdesc);
	$arr['pagingLink'] = getPagingLink($sql, $arr['ndata'], $page);
	$sql = mysql_query($sql) OR die(output(mysql_error()));

	$arr['nrows'] = mysql_num_rows($sql);
	$arr['nfields'] = mysql_num_fields($sql);
	$arr['npage'] = ceil ($arr["totaldata"] / $arr['nrows']);
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

?>