<?php
require "conf.php";
require "func.php";

$lat = isset($_GET["gps_lat"]) ? str_clean($_GET["gps_lat"],1) : "0";
$lng = isset($_GET["gps_lng"]) ? str_clean($_GET["gps_lng"],1) : "0";

$sql = "select location.*, sqrt(pow((loc_lat-".$lat."),2) + pow((loc_lng-".$lng."),2)) as distance  from location where loc_lat<>0 order by distance limit 1;";

	//die($sql);
	$sql = mysql_query($sql) OR die(output(mysql_error()));
	
	$arr["totaldata"] = mysql_num_rows($sql);
	$arr['ndata'] = $ndata == 0 ? $arr["totaldata"] : $ndata;

	$arr['nrows'] = mysql_num_rows($sql);
	$arr['nfields'] = mysql_num_fields($sql);
	$arr['npage'] = 1;
	$arr['page'] = 1;
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