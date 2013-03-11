<?php

	require "conf.php";
	require "func.php";

	$id = isset($_GET["id"]) ? str_clean(strtoupper($_GET["id"])) : "ID";
	$ndata = isset($_GET["ndata"]) ? str_clean(strtoupper($_GET["ndata"])) : 0;
	$page = isset($_GET["page"]) ? str_clean(strtoupper($_GET["page"])) : 1;
	$order = isset($_GET["order"]) ? str_clean(strtoupper($_GET["order"])) : "country_name";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean(strtoupper($_GET["ascdesc"])) : "ASC";
	$callback = isset($_GET['callback']);

	$sql = "SELECT * FROM $t_country";
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	$arr["totaldata"] = mysql_num_rows($sql);
	$arr['ndata'] = $ndata == "0" ? $arr["totaldata"] : $ndata;
	
	$sql = "SELECT * FROM $t_country";
	
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