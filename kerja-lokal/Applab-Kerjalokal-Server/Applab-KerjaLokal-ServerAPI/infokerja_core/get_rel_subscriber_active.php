<?php

	require "conf.php";
	require "func.php";

	$key = isset($_GET["key"]) ? str_clean($_GET["key"]) : "";
	$value = isset($_GET["value"]) ? str_clean($_GET["value"]) : "";
	$ndata = isset($_GET["ndata"]) ? str_clean($_GET["ndata"],1) : 0;
	$page = isset($_GET["page"]) ? str_clean($_GET["page"],1) : 0;
	$order = isset($_GET["order"]) ? str_clean($_GET["order"]) : "rel_id";
	$ascdesc = isset($_GET["ascdesc"]) ? str_clean($_GET["ascdesc"]) : "ASC";

	$sql = "SELECT * FROM $t_rel_subscriber_cat";

	if ($key != "") 
	{
		//$sql .= " WHERE (status = 1 OR status = 2 OR status = 3) AND ";
		$sql .= " WHERE (n_jobreceived < 8) AND ";
		$key = explode("|",$key);
		$value = explode("|",$value);
		$a = 0;
		foreach ($key as $key2)
		{ 
			$sql .= $key2." = \"$value[$a]\""; 
			$a++;
			if ($a < sizeof($key)) $sql .= " AND ";
		}
	}

	//die($sql);
	$q = mysql_query($sql) OR die(output(mysql_error()));
	$arr["totaldata"] = mysql_num_rows($q);
	$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;

	$sql = getPagingQuery($sql,$ndata,$page,$order,$ascdesc);
	$sql = mysql_query($sql) OR die(output(mysql_error()));

	$arr['nrows'] = mysql_num_rows($sql);
	$arr['nfields'] = mysql_num_fields($sql);
	$arr['npage'] = $ndata > 0 ? ceil($arr["totaldata"] / $ndata) : 1;
	$arr['page'] = $page;
	$arr['results'] = array();

	//$i = 0;
	while($row = mysql_fetch_assoc($sql))
	{
		for($j=0;$j<$arr['nfields'];$j++)
		{
			$val[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
		}
		array_push($arr["results"], $val);
		//$i++;
	}
	echo output($arr);
	//echo "<pre>"; print_r(json_decode(output($arr), true)); echo "</pre>";

?>